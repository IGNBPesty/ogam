/**
 * Licensed under EUPL v1.1 (see http://ec.europa.eu/idabc/eupl).
 *
 * © European Union, 2008-2012
 *
 * Reuse is authorised, provided the source is acknowledged. The reuse policy of the European Commission is implemented by a Decision of 12 December 2011.
 *
 * The general principle of reuse can be subject to conditions which may be specified in individual copyright notices.
 * Therefore users are advised to refer to the copyright notices of the individual websites maintained under Europa and of the individual documents.
 * Reuse is not applicable to documents subject to intellectual property rights of third parties.
 */

/**
 * The class of the details panel.
 * This class is required because the panel class
 * can't be closed but the panel extended class can.
 * 
 * @class Genapp.DetailsPanel
 * @extends Ext.Panel
 * @constructor Create a new DetailsPanel
 * @param {Object} config The config object
 */
Ext.define('OgamDesktop.view.navigation.Tab', {
	xtype: 'navigation-tab',
	extend: 'Ext.panel.Panel',
	layout: 'card',
    /**
     * Internationalization.
     */ 
    editLinkButtonTitle : 'Edit this data',
    editLinkButtonTip : 'Edit this data in the edition page.',
    /**
     * @cfg {Number} headerWidth
     * The tab header width. (Default to 60)
     */
    headerWidth:60,
    /**
     * @cfg {Boolean} closable
     * Panels themselves do not directly support being closed, but some Panel subclasses do (like
     * {@link Ext.Window}) or a Panel Class within an {@link Ext.TabPanel}.  Specify true
     * to enable closing in such situations. Defaults to true.
     */
    closable: true,
    /**
     * @cfg {Boolean} autoScroll
     * true to use overflow:'auto' on the panel's body element and show scroll bars automatically when
     * necessary, false to clip any overflowing content (defaults to true).
     */
    autoScroll:true,
    /**
     * @cfg {String} dataUrl
     * The url to get the details.
     */
    dataUrl:null,
    /**
     * @cfg {String} pdfUrl
     * The url to get the pdf.
     */
    pdfUrl: 'pdfexport',
    /**
     * @cfg {String} cls
     * An optional extra CSS class that will be added to this component's Element (defaults to 'genapp-query-details-panel').
     * This can be useful for adding customized styles to the component or any of its children using standard CSS rules.
     */
//    cls:'genapp-query-details-panel',
    /**
     * @cfg {String} hideSeeChildrenButton
     * True to hide the see children button (defaults to <tt>false</tt>)
     */
    hideSeeChildrenButton: false,
    /**
     * @cfg {String} seeChildrenButtonTip
     * The see Children Button Tip (defaults to <tt>'Display the children of the data into the grid details panel.'</tt>)
     */
    seeChildrenButtonTip: 'Display the children of the data into the grid details panel.',
    /**
     * @cfg {String} seeChildrenButtonTitleSingular
     * The see Children Button Title Singular (defaults to <tt>'See the only child'</tt>)
     */
    seeChildrenButtonTitleSingular: 'See the only child',
    /**
     * @cfg {String} seeChildrenButtonTitlePlural
     * The see Children Button Title Plural (defaults to <tt>'See the children'</tt>)
     */
    seeChildrenButtonTitlePlural: 'See the children',
    /**
     * @cfg {Number} tipDefaultWidth
     * The tip Default Width. (Default to 300)
     */
    tipDefaultWidth: 300,
    /**
     * @cfg {Number} titleCharsMaxLength
     * The title Chars Max Length. (Default to 8)
     */
    titleCharsMaxLength : 8,
    /**
     * @cfg {String} loadingMsg
     * The loading message (defaults to <tt>'Loading...'</tt>)
     */
    loadingMsg: 'Loading...',
    dockedItems: [{
        xtype: 'toolbar',
        dock: 'right',
                items: [{
                    xtype: 'menu',
            		closable: true,
            		header: true,
//            	    layout: 'border',
                	title: 'Parents',
                	titleCollapse: true,
                	collapsible: true,
                    floating: false,
                	items: [{
                    		xtype: 'menuitem',
                    		text: 'orga 1',
                    		iconCls: 'icon-delete'
		    		    },{
		    		    	xtype: 'menuitem',
		    		    	text: 'Ajouter un organisme',
		    		    	iconCls: 'icon-add'
		    		    }]
                },{
                    xtype: 'menu',
                	title: 'Children',
                    floating: false,
                    listeners: {
                    	render: function() {
                    		Ext.Ajax.request({
                    		    url: Ext.manifest.OgamDesktop.requestServiceUrl +'ajaxgetchildren',
                    			actionMethods: {create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'},
                    			reader: {
                    				type: 'json',
                    				rootProperty: ''
                    			},
                    		    params: {
                    		        id: 'SCHEMA/RAW_DATA/FORMAT/LOCATION_DATA/PROVIDER_ID/1/PLOT_CODE/01575-14060-4-0T'
                    		    },
                    		    success: function(response){
                    		        var text = response.responseText;
                    		        
                    		        console.log(text);
                    		        // process server response here
                    		    }
                    		});
                    	}
                    }
                }]
        }],
    // private
    initComponent : function() {
    	
        this.title = '<div style="width:'+ this.headerWidth + 'px;">'+this.loadingMsg+'</div>';
        this.on('render', this.updateDetails, this);
        this.itemId = this.rowId;
        /**
         * @cfg {Ext.XTemplate} tpl
         * A {@link Ext.XTemplate} used to setup the details panel body.
         */
        

       
        this.tpl = new Ext.XTemplate(
//			'<legends style="display:block; position:absolute; left:1px; top:621px">',
			'<legends style="display:block; position:absolute; left:1px; top:1px">',
				'<tpl for="formats">',
					'<fieldset>',
						'<legend>',
							'<div>{title}</div>',
						'</legend>',
						'<div class="genapp-query-details-panel-fieldset-body">',
							'<tpl for="fields">',
								'<tpl if="type != \'IMAGE\'">',
									'<p><b>{label} :</b> {[(Ext.isEmpty(values.value) || (Ext.isString(values.value) && Ext.isEmpty(values.value.trim()))) ? "-" : values.value]}</p>',
								'</tpl>',
//								'<tpl if="type == \'IMAGE\'">', 
//									'{[(Ext.isEmpty(values.value) || (Ext.isString(values.value) && Ext.isEmpty(values.value.trim()))) ? \'\' : \'<img class=\"genapp-query-details-image-field\" title=\"\' + values.label + \'\" src=\"' + Genapp.base_url + '/img/photos/\' + values.value + \'\">\']}',
//								'</tpl>',
							'</tpl>',
						'</div>',
					'</fieldset>',
				'</tpl>',
            '</legends>',
//          '<tpl style="display:block" for="maps1.urls">',
//        	'<img style="display:block; position:absolute; left:1px; top:1px" title="title" src="{url}">',
//        '</tpl>',
//        '<tpl for="maps2.urls">',
//        	'<img style="display:block; position:absolute; left:1px; top:311px" title="title" src="{url}">',
//        '</tpl>',
            {
                compiled: true,      // compile immediately
                disableFormats: true // reduce apply time since no formatting
            }
        );

       this.callParent(arguments);
    },

    /**
     * Updates the Details panel body
     * 
     * @param {Ext.Panel} panel The details panel
     */
    
    updateDetails : function(panel) {
//        this.getUpdater().showLoading();
        console.log('rowId', this.rowId);
        Ext.Ajax.request({
        	url: Ext.manifest.OgamDesktop.requestServiceUrl +'ajaxgetdetails',
			actionMethods: {create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'},
            success :function(response, options){
                var details = Ext.decode(response.responseText);
                
                var title = details.title;
                if(details.title.length > this.titleCharsMaxLength){
                    title = details.title.substring(0,this.titleCharsMaxLength) + '...';
                }
                this.setTitle('<div style="width:'+ this.headerWidth + 'px;"'
                    +' ext:qtip="' + details.title + '"'
                    +'>'+title+'</div>');
                this.tpl.overwrite(this.body, details);
            },
            method: 'POST',
            params : {id : this.rowId},
            scope :this
        });
    },

    /**
     * Export the details panel as PDF
     */
//    exportAsPDF : function(){
//        document.location.href = Genapp.ajax_query_url + this.pdfUrl + '?id=' + this.rowId;
//    }
});