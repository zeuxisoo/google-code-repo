function writeMessage() {

	var bookForm = new Ext.FormPanel({
						labelAlign: 'top',
						frame:true,
						bodyStyle:'padding:5px 5px 0',
						width: 600,
						items: [{
							layout:'column',
							items:[{
								columnWidth:.5,
								layout: 'form',
								items: [{
									xtype:'textfield',
									fieldLabel: '你的暱稱',
									name: 'username',
									allowBlank:false,
									anchor:'95%'
								}, {
									xtype:'textfield',
									fieldLabel: '留言標題',
									name: 'title',
									allowBlank:false,
									anchor:'95%'
								}]
							},{
								columnWidth:.5,
								layout: 'form',
								autoHeight:true,
								items: [
									new Ext.form.ComboBox({
											store:new Ext.data.SimpleStore({
													fields: ["sex_id", "sex_name"],
													data:[[1,'男孩'],[2,'女孩']]
												}),
										valueField:"sex_id",
										displayField:"sex_name",
										mode:'local',	// 讀取程式內的資料 不存取伺服器
										forceSelection: true,
										blankText:'請選擇性別',
										emptyText:'請選擇性別',
										editable:false,
										triggerAction:'all',	// 每次也顯示所有資料
										allowBlank:true,
										fieldLabel:'你的性別',
										name:'sex'
									}),
								{
									xtype:'textfield',
									fieldLabel: '電郵地址',
									name: 'email',
									vtype:'email',
									anchor:'95%'
								}]
							}]
						},{
							//xtype:'htmleditor',
							xtype:'textarea',
							fieldLabel:'留言內容',
							height:300,
							name:'comment',
							allowBlank:false,
							anchor:'98%'
						}],
						buttons: [
							{
								text:'留言',
								handler:function(){
									if(bookForm.form.isValid()) {
										this.disable();
										Ext.MessageBox.show({
											msg:'正在儲存留言中，請等候...',
											progressText:'Saving...',
											width:300,
											wait:true,
											waitConfig:{interval:200},
											animEl:'saving'
										});
										setTimeout(function(){}, 10000);
										bookForm.form.doAction('submit',{
											url:'index.php?sk=add',
											method:'post',
											params:'',
											success:function(form,action) {
												if (action.result.msg == 'ok') {
													Ext.MessageBox.hide();
													Ext.Msg.alert('提示','發佈留言完成');
													ds.reload();
												}else{
													Ext.MessageBox.show({
														title:'提示',
														msg:action.result.msg,
														buttons: Ext.MessageBox.OK,
														icon: 'ext-mb-error'
													});
												}
											},
											failure:function() {
												Ext.Msg.alert('提示','伺服器發生不可預知的錯誤,請晚點再試!');
											}
										});
									}
								}
							},
							{
								text:'重填',
								handler:function() {
									bookForm.form.reset();
								}
							}
						]
					});

	var postWin = new Ext.Window({
						title:'撰寫留言',
						width:600,
						height:530,
						collapsible:true,
						maximizable:true,
						layout:'fit',
						plain:true,
						bodyStyle:'padding:5px;',
						modal:true,
						items: bookForm
					}).show();
}

// 
function checkComment(num) {
	return num == 0 ? '<font color="#666666">0</font>' : num;
}

//
function getId(gridInfo) {
	var s = gridInfo.getSelectionModel().getSelected();
	return s ? s.id : 0;
}

//
function getRowObject(gridInfo) {
	return gridInfo.getSelectionModel().getSelected().data;
}

//
function viewMessage() {
	var id = getId(grid);
	if (id) {
		var n = panelMessageDetail.getComponent(id);
		var g = getRowObject(grid);
		panelMessageDetail.add({
			'id':id,
			'title':g.title,
			closable:true,
			tbar: [
				{text:'回覆',iconCls:'reply',handler:function() { replyMessage(g, id); }},
				{xtype:'tbseparator'},
				{text:'刷新',iconCls:'refresh',handler:function() { document.getElementById('reply_'+id).src='index.php?sk=view&cid='+id; } },
				{xtype:'tbseparator'},
				{xtype:'tbtext',text:'發佈時間:'+g.adddate,buttonAlign:'right'}
			],
			html:'<iframe scrolling="auto" id="reply_'+id+'" frameborder="0" width="100%" height="100%" src=index.php?sk=view&cid='+id+'></iframe>'
		});
		panelMessageDetail.setActiveTab(id);
	}else{
		Ext.Msg.alert('錯誤', '請選擇一條留言查看！');
	}
}

//
function replyMessage(obj, tabId) {
	var replyForm = new Ext.FormPanel({
						labelAlign: 'top',
						frame:true,
						bodyStyle:'padding:5px 5px 0',
						width: 600,
						items: [{
							layout:'column',
							items:[{
								columnWidth:.5,
								layout: 'form',
								items: [{
									xtype:'textfield',
									fieldLabel: '你的暱稱',
									name: 'username',
									allowBlank:false,
									anchor:'95%'
								}, {
									xtype:'textfield',
									fieldLabel: '留言標題',
									name:'title',
									value:obj.title,
									readOnly:true,
									allowBlank:false,
									anchor:'95%'
								}]
							},{
								columnWidth:.5,
								layout: 'form',
								autoHeight:true,
								items: [
									new Ext.form.ComboBox({
											store:new Ext.data.SimpleStore({
													fields: ["sex_id", "sex_name"],
													data:[[1,'男孩'],[2,'女孩']]
												}),
										valueField:"sex_id",
										displayField:"sex_name",
										mode:'local',	// 讀取程式內的資料 不存取伺服器
										forceSelection: true,
										blankText:'請選擇性別',
										emptyText:'請選擇性別',
										editable:false,
										triggerAction:'all',	// 每次也顯示所有資料
										allowBlank:true,
										fieldLabel:'你的性別',
										name:'sex'
									}),
								{
									xtype:'textfield',
									fieldLabel: '電郵地址',
									name: 'email',
									vtype:'email',
									anchor:'95%'
								}]
							}]
						},{
							xtype:'textarea',
							fieldLabel:'留言內容',
							height:80,
							name:'comment',
							allowBlank:false,
							anchor:'98%'
						}],
						buttons: [
							{
								text:'回覆',
								handler:function(){
									if(replyForm.form.isValid()) {
										this.disable();
										Ext.MessageBox.show({
											msg:'正在儲存回覆中，請等候...',
											progressText:'Saving...',
											width:300,
											wait:true,
											waitConfig:{interval:200},
											animEl:'saving'
										});
										setTimeout(function(){}, 10000);
										replyForm.form.doAction('submit',{
											url:'index.php?sk=reply&cid='+obj.cid,
											method:'post',
											params:'',
											success:function(form,action) {
												if (action.result.msg == 'ok') {
													Ext.MessageBox.hide();
													Ext.Msg.alert('提示','發佈回覆完成');
													ds.reload();
													document.getElementById('reply_'+tabId).src='index.php?sk=view&cid='+obj.cid;
												}else{
													Ext.MessageBox.show({
														title:'提示',
														msg:action.result.msg,
														buttons: Ext.MessageBox.OK,
														icon: 'ext-mb-error'
													});
												}
											},
											failure:function() {
												Ext.Msg.alert('提示','伺服器發生不可預知的錯誤,請晚點再試!');
											}
										});
									}
								}
							},
							{
								text:'重填',
								handler:function() {
									replyForm.form.reset();
								}
							}
						]
					});

	var replyWin = new Ext.Window({
						title:'回覆留言',
						width:600,
						height:300,
						collapsible:true,
						maximizable:true,
						layout:'fit',
						plain:true,
						bodyStyle:'padding:5px;',
						modal:true,
						items: replyForm
					}).show();
}