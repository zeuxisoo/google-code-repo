<?php
/*
 * Author : Zeuxis Lo
 * Data   : 2007/09/28 21:04
 * Version: v0.001 Beta
 * P-Name : Seek Template Class
 *
 * ====
 * [Fix] 無法讀取檔案時輸出錯誤,而不是找不到伺服器
 */
class SeekTemplate {

	var $dir_tpl  = 'template';
	var $dir_tpl_c= 'template_c';
	var $dir_skin = '';
	
	var $settag     = true;
	
	var $tpldata;
	var $tplfile;
	var $objfile;

	function seektemplate() {
	}

	function error($num) {
		switch($num) {
			case 1:
				$str = 'Can Not Create File Cache : '.$this->tplfile;
				break;
			case 2:
				$str = 'Can Not Read Template File : '.$this->tplfile;
				break;
			case 3:
				$str = 'Can not open template file : '.$this->tplfile;
				break;
		}

		exit("<font size='2'>$str</font>");
	}
	
	function save() {
		if(!$fp = @fopen($this->objfile, 'wb')) $this->error(1);
		@flock($fp, LOCK_EX);
		fwrite($fp, $this->tpldata);
		@flock($fp, LOCK_UN);
		fclose($fp);
		return $this->objfile;
	}

	function compile() {
		$var = '(\$[a-zA-Z_][a-zA-Z0-9_\->\.\[\]\'\$]*)';
		$search  = array(
					'#{(\$[a-zA-Z_][a-zA-Z0-9_\->\.\[\]\'\$]*)}#s',
					'#{'.$var.':nl2br}#i',
					'#{'.$var.':fixhtml}#i',
					'#{'.$var.':nl2br_fixhtml}#i',
					'#{'.$var.':dateFormat:\'(.+?)\'}#i',
					'#{set:(.+?)}#i',
					'#<!--{template:(.*?)}-->#i',
					'#<!--{include:(.*?)}-->#i',
					'#<!--{func:(.*?)}-->#i',
					'#<!--{call:(.*?)}-->#i',
					'#<!--{foreach:(\S+)\s+(\S+)\s+(\S+)\}-->#i',
					'#<!--{for:(.*?)\;(.*?)\;(.*?)}-->#i',
					'#<!--{if:(.*?)}-->#i',
					'#<!--{elseif:(.*?)}-->#i',
					'#<!--{else}-->#i',
					'#<!--{baseif:(.*?):(.*?):(.*?)}-->#i',
				);

		$replace = array(
					'<?php echo \1; ?>',
					'<?php echo nl2br(\1); ?>',
					'<?php echo htmlspecialchars(\1); ?>',
					'<?php echo nl2br(htmlspecialchars(\1)); ?>',
					'<?php echo gmdate("\2",\1+$timezone*3600); ?>',
					'<?php \1; ?>',
					'<?php include_once $tpl->display(\'\1\'); ?>',
					'<?php include_once \'\1\'; ?>',
					'<?php \1; ?>',
					'<?php echo \1; ?>',
					'<?php if (is_array(\1)) { foreach(\1 as \2 => \3) { ?>',
					'<?php for(\1;\2;\3) { ?>',
					'<?php if (\1) { ?>',
					'<?php } elseif (\1) { ?>',
					'<?php } else { ?>',
					'<?php echo (\1) ? \2 : \3; ?>',
				);

		$this->tpldata = preg_replace($search, $replace, $this->tpldata);

		$search2 = array(
					'#<!--{/foreach}-->#i',
					'#<!--{/for}-->#i',
					'#<!--{/if}-->#i',
				);

		$replace2= array(
					'<?php } } ?>',
					'<?php } ?>',
					'<?php } ?>',
				);

		$this->tpldata = preg_replace($search2, $replace2, $this->tpldata);
		$this->tpldata = ($this->settag == true) ? preg_replace("/([\n\r]+)\t+/s", "\\1", $this->tpldata) : $this->tpldata;

		return $this->save();
	}

	function display($files, $dirs = '') {
		$dirs = $dirs ? $dirs : $this->dir_skin;

		$this->tplfile  = $this->dir_tpl.'/'.$dirs.'/'.$files;
		$this->objfile  = $this->dir_tpl_c.'/'.$dirs.'/'.$files.'.php';

		if ($dirs == 'default' && !file_exists($this->tplfile)) {
			$this->error(3);
		}

		if (!file_exists($this->tplfile) && !empty($this->dir_skin)) {
			return $this->display($files, 'default');
		}

		if (@filemtime($this->tplfile) <= @filemtime($this->objfile)) {
			return $this->objfile;
		} else {
			$this->tpldata = file_get_contents($this->tplfile);

			if (!$this->tpldata) { exit("Can not read ".$this->tplfile); }

			if (!file_exists($this->dir_tpl_c.'/'.$dirs) && !empty($this->dir_skin)) {
				mkdir($this->dir_tpl_c.'/'.$dirs, 0777);
			}

			return $this->compile();
		}
	}

}
?>