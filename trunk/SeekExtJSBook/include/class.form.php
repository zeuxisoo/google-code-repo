<?php
/*
 * Author : Zeuxis Lo
 * Data   : 6/7/2008 15:23 PM
 * Version: v0.001 Beta
 * P-Name : Seek Form Class
 *
 * ==========================
 */
class FormCore {
	var $html    = '';
	var $sk      = '';
	var $op      = '';
	var $action  = '';
	var $colspan = 2;
	var $isUpload= false;

	//
	function addRadio($label, $name, $defaultValue, $checkedValue = '') {
		if (is_array($defaultValue)) {
			foreach($defaultValue as $k => $v) {
				$temp .= "<input type='radio' name='".$name."' value='".$k."'".( $checkedValue == $v ? ' checked="checked"' : null )." /> $v";
			}
		}
		$this->addTd($label.' : ', $temp);
	}

	//
	function addTText($text, $align = 'left') {
		$this->addRow($text, $align);
	}

	//
	function addInput($label, $name, $valid = '', $value = '', $type = 'text') {
		$valid = empty($valid) ? null : " valid='".$valid."'";
		$this->addTd($label.' : ', "<input type='".$type."' name='".$name."' value='".$value."' class='input'{$valid} />");
	}

	//
	function addTableHeader($name = '', $colspan = 2, $width = '80%', $cellpadding = 5, $cellspacing = 1, $cls = array('i_table', 't_head')) {
		$this->setColspan($colspan);

		$this->addHtml('<div id="'.str_replace('_','-', $cls[0]).'" class="'.$cls[0].'" style="width:'.$width.'">');
		$this->addHtml('<table width="100%" cellpadding="'.$cellpadding.'" cellspacing="'.$cellspacing.'" border="0">');

		if (!empty($name)) {
			$this->addHtml('<tr>');
			$this->addHtml('<td class="'.$cls[1].'" colspan="'.$this->colspan.'">'.$name.'</td>');
			$this->addHtml('</tr>');	
		}
	}

	function addTableFooter() {
		$this->addHtml('</table>');
		$this->addHtml('</div>');		
	}

	//
	function addFormHidden($hidden = '') {
		if (is_array($hidden)) {
			foreach($hidden as $k => $v) {
				$this->addHtml('<input type="hidden" name="'.$k.'" value="'.$v.'" />');
			}
		}else{
			$this->addFormHidden(array('sk'=>$this->sk, 'op'=>$this->op, 'action'=>$this->action));
		}
	}

	function addFormHeader($action, $script = '', $script_mode = 3, $method = 'post') {
		switch($script) {
			case 'continue':
				$script = "return confirm('Continue !?') ? true : false;";
				break;
			case 'remove':
				$script = "return confirm('Delete !?') ? true : false;";
				break;
			case 'input':
				$script = 'return SeekVForm.Validate(this, '.$script_mode.')';
				break;
			default:
				$script = null;
				break;
		}

		$script  = empty($script) ? null : ' onsubmit="'.$script.'"';
		$script .= $this->isUpload ? ' enctype="multipart/form-data"' : null;
		$this->addHtml("<form name='myform' id='myform' action='".$action."' method='".$method."'{$script}>");
	}

	function addFormFooter() {
		$this->addHtml('</form>');
	}

	function addTd($label, $html) {
		$this->addHtml('<tr class="t_body" onmouseover="this.className=\'t_body2\'" onmouseout="this.className=\'t_body\'">');
		$this->addHtml('<td align="right" width="30%"style="padding-right: 10px;">'.$label.'</td>');
		$this->addHtml('<td align="left" width="70%" style="padding-left: 10px;">');
		$this->addHtml($html);
		$this->addHtml('</td>');
		$this->addHtml('</tr>');
	}

	function addRow($row, $align='left') {
		$this->addHtml('<tr class="t_body" onmouseover="this.className=\'t_body2\'" onmouseout="this.className=\'t_body\'">');
		$this->addHtml('<td align="'.$align.'" colspan="'.$this->colspan.'" style="padding: 5px;">');
		$this->addHtml($row);
		$this->addHtml('</td>');
		$this->addHtml('</tr>');	
	}

	function addBr($number = 1) {
		$this->addHtml(str_repeat('<br />', $number));
	}

	function addHtml($html) {
		$this->html .= $html."\n";
	}

	function addScript($script) {
		$this->html .= "<script language='javascript' type='text/javascript'>";
		$this->html .= $script;
		$this->html .= "</script>";
	}

	function setSK($sk) {
		$this->sk = $sk;
	}

	function setOP($op) {
		$this->op = $op;
	}

	function setAction($action) {
		$this->action = $action;
	}

	function setColspan($number) {
		$this->colspan = $number;
	}

	function setIsUpload() {
		$this->isUpload = true;
	}

	function display() {
		echo $this->html;
	}
}

//
class SeekForm extends FormCore {

	function addTMenu($menu) {
		if (is_array($menu)) {
			$this->addHtml('<tr class="t_body" onmouseover="this.className=\'t_body2\'" onmouseout="this.className=\'t_body\'">');
			foreach($menu as $name) {
				if (empty($name)) {
					$name  = '<a href="#" onClick="checkAll(\'myform\', true);">[√]</a>';
					$name .= '<a href="#" onClick="checkAll(\'myform\', false);">[Χ]</a>';
				}
				$this->addHtml('<td align="center"><b>'.$name.'</b></td>');
			}
			$this->addHtml('<tr>');
		}
	}

	function addTData($data, $postValue, $checkedType = 0, $postName = 'del[]') {
		if (is_array($data)) {
			$checked = $checkedType ? ' checked="checked"' : null;
			$this->addHtml('<tr class="t_body" onmouseover="this.className=\'t_body2\'" onmouseout="this.className=\'t_body\'">');
			foreach($data as $name) {
				if (empty($name)) $name = '<input type="checkbox" name="'.$postName.'" value="'.$postValue.'"'.$checked.' />';
				$this->addHtml('<td align="center" style="height: 25px;">'.$name.'</td>');
			}
			$this->addHtml('<tr>');
		}	
	}

	function addTDataBackup($data, $checkedType = 0) {
		if (is_array($data)) {
			$checked = $checkedType ? ' checked="checked"' : null;
			$this->addHtml('<tr class="t_body" onmouseover="this.className=\'t_body2\'" onmouseout="this.className=\'t_body\'">');
			foreach($data as $name) {
				if (empty($name)) $name = '<input type="checkbox" name="table['.$data[1].']" value="yes" datalength="'.$data[2].'" onclick="getSize()"'.$checked.' />';
				$this->addHtml('<td align="center" style="height: 25px;">'.$name.'</td>');
			}
			$this->addHtml('<tr>');
		}	
	}

	function addTSubmit($submit, $reset = '', $align = 'right') {
		$temp = '';
		if ($submit) $temp .= '<input type="submit" value="'.$submit.'" class="t_button" />';
		if ($reset) $temp .= '<input type="reset" value="'.$submit.'" class="t_button" />';
		$this->addRow($temp, $align);
	}

	function addTBr($number = 1) {
		$this->addRow(str_repeat('<br />', $number));
	}

	//
	function addTHeader($name = '', $colspan = 2, $width = '80%', $cellpadding = 5, $cellspacing = 1) {
		$this->addTableHeader($name, $colspan, $width, $cellpadding, $cellspacing);
	}

	function addTFooter() {
		$this->addTableFooter();
	}

	//
	function addFHeader($phpself, $action = '', $type = 'input') {
		$this->addFormHeader($phpself, $type);
		$this->addFormHidden(array('sk'=>$this->sk, 'op'=>$this->op, 'action'=>empty($action) ? '' : $action));
	}

	function addFFooter() {
		$this->addFormFooter();
		$this->display();
	}

}
?>