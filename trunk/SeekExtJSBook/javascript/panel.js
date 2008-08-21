// Message Detail->Grid
var ds = new Ext.data.Store({
				proxy : new Ext.data.HttpProxy({url:'index.php?sk=list'}),
				reader: new Ext.data.JsonReader({
							root: 'rows',
							totalProperty: 'results',
							id: 'cid'
						},['cid','username','title','sex','replynum','adddate'])
		});
ds.load();

var colModel = new Ext.grid.ColumnModel([
					new Ext.grid.RowNumberer(),
					{ header: 'ID',			width: 50,	sortable: true,	dataIndex: 'cid' },
					{ header: '標題',		width: 300,	sortable: true,	dataIndex: 'title' },
					{ header: '作者',		width: 150,	sortable: true,	dataIndex: 'username' },
					{ header: '回覆數量',	width: 150,	sortable: true,	dataIndex: 'replynum',	renderer:checkComment },
					{ header: '留言時間',	width: 50,	sortable: true,	dataIndex: 'adddate' }
				]);

var grid = new Ext.grid.GridPanel({
				border:false,
				buttonAlign :'left',
				region:'center',
				loadMask: true,
				store: ds,
				autoExpandColumn:5,
				cm: colModel,
				autoScroll: true,
				tbar: [
					{ text:'查看',	iconCls:'copy',		handler:viewMessage },
					{ xtype:'tbseparator' },
					{ text:'更新',	iconCls:'refresh',	handler:function (){ ds.reload(); } }
				],
				bbar: new Ext.PagingToolbar({
							pageSize: 20,
							store: ds,
							displayInfo: true,
							displayMsg: '第{0} 到 {1} 條留言 共{2}條',
							emptyMsg: '沒有留言'
						})
});
grid.on('rowdblclick', viewMessage, grid);

// Panel->Action->Message Detail
var panelMessageDetail = new Ext.TabPanel({
							region:'center',
							deferredRender:false,
							activeTab:0,
							resizeTabs:true,
							enableTabScroll:true,
							items:[
								{
									id:'ViewBook',
									title: '查看留言',
									layout:'fit',
									items: grid,
									autoScroll:true
								}
							]
						});
panelMessageDetail.setActiveTab('ViewBook');

// Panel Setting
var panel = {
				region:'west',
				id:'left-panel',
				title:'Panel',
				split:true,
				width: 200,
				minSize: 175,
				maxSize: 400,
				collapsible: true,
				margins:'0 0 0 5',
				layout:'accordion',
				layoutConfig:{
					animate:true
				},
				items: [
					{
						id:'panel-action',
						title:'Action',
						border:false,
						iconCls:'nav'
					},
					{
						id:'panel-about',
						title:'About',
						border:false,
						iconCls:'settings'
					}
				]
			}

// Action Tree Method
function treeActionClick(node,e) {
	if(node.isLeaf()) {
		e.stopEvent();

		if (node.id == 'WriteBook') {
			writeMessage();
			return;
		}

		var n = panelMessageDetail.getComponent(node.id);
		if (!n) {
			var n = panelMessageDetail.add({
				'id' : node.id,
				'title' : node.text,
				closable:true,
				html : '發生錯誤在 "'+node.text+'"'
				});
		}
		panelMessageDetail.setActiveTab(n);
		if (node.id == 'ViewBook') ds.reload();
	}
}