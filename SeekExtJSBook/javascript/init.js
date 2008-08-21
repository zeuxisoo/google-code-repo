Ext.BLANK_IMAGE_URL = 'images/blank.gif';

Ext.onReady(function() {

	// Global Detail
	var viewPort = new Ext.Viewport({
		layout:'border',
		items:[panelMessageDetail, panel]
	});
	viewPort.render();

	// Action->About->Method
	var win;
	var aboutWin = function() {
					if(!win){						
						win = new Ext.Window({
							el:'window-about',
							title:'程式簡介',
							layout:'fit',
							width:250,
							height:150,
							minWidth:200,
							minHeight:150,
							bodyStyle:'padding:5px;',
							maximizable:false,
							closeAction:'hide',
							collapsible:true,
							plain:true,
							modal:true, // 鎖住畫面
							buttonAlign:'center',

							items: new Ext.Panel({
									contentEl:'window-about-panel',
									width: 300,
									height: 300,
									border:false
							}),

							buttons: [{
								text: '關閉',
								handler: function(){
									win.hide();
								}
							}]
						});
					}
					win.show(this);
				};

	// Action->About->Tree
	var rootAbout   = new Ext.tree.TreeNode({id:'rootAbout'});
	var aboutData_1 = new Ext.tree.TreeNode({
				id:'aboutData-1',
				href:'http://skhk.uni.cc/',
				hrefTarget:'_blank',
				text:'作者網站'
			});
	var aboutData_2 = new Ext.tree.TreeNode({
				id:'aboutData-2',
				listeners:{'click':aboutWin},
				text:'程式簡介'
			});

	rootAbout.appendChild(aboutData_1);
	rootAbout.appendChild(aboutData_2);

	var treeAbout = new Ext.tree.TreePanel({
		width:200,
		margins:'5',
		cmargins:'5',
		root:treeAbout,
		animate:true,
		enableDD:false,
		border:true,
		rootVisible:false,
		containerScroll: true,
		el:'panel-about'
	});

    treeAbout.setRootNode(rootAbout);
    treeAbout.render();
	rootAbout.expand();

	// Panel->Action->Tree
	var treeAction = new Ext.tree.TreePanel({
		el : 'panel-action',
		autoScroll : true,
		animate : true,
		enableDD : false,
		containerScroll : true,
		loader : new Ext.tree.TreeLoader({
			dataUrl : './javascript/actionTree.js'
		})
	});

	var rootAbout = new Ext.tree.AsyncTreeNode({
		text : 'Action List',
		draggable : false,
		id : 'source'
	});

    treeAction.setRootNode(rootAbout);
    treeAction.render();
    rootAbout.expand();
	treeAction.on('click',treeActionClick);

});