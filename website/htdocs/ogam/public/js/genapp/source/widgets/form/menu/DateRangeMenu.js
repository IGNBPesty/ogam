/**
 * 
 * A menu containing a {@link Genapp.form.picker.DateRangePicker} Component.
 * 
 * @class Genapp.form.menu.DateRangeMenu
 * @extends Ext.menu.DateMenu
 * @constructor Create a new DateRangeMenu
 * @param {Object} config
 * @xtype daterangemenu
 */

Ext.namespace('Genapp.form.menu');

Genapp.form.menu.DateRangeMenu = Ext.extend( Ext.menu.DateMenu, {
    /**
     * @cfg {String/Object} layout
     * Specify the layout manager class for this container either as an Object or as a String.
     * See {@link Ext.Container#layout layout manager} also.
     * Default to 'table'.
     * Note: The layout 'menu' doesn't work on FF3.5,
     * the rangePicker items are not rendered 
     * because the rangePicker is hidden... 
     * But it's working on IE ???
     */
    layout:'table', 
    /**
     * @cfg {String} cls
     * An optional extra CSS class that will be added to this component's Element (defaults to 'x-date-range-menu').
     * This can be useful for adding customized styles to the component or any of its children using standard CSS rules.
     */
    cls: 'x-date-range-menu',

    // private
    initComponent: function(){
        this.on('beforeshow', this.onBeforeShow, this);
        /**
         * The {@link Genapp.form.picker.DateRangePicker} instance for this DateRangeMenu
         * @property rangePicker
         * @type Genapp.form.picker.DateRangePicker
         */
        Ext.apply(this, {
            plain: true,
            showSeparator: false,
            items: [this.rangePicker = new Genapp.form.picker.DateRangePicker(this.initialConfig)]
        });
        this.rangePicker.purgeListeners();
        Ext.menu.DateMenu.superclass.initComponent.call(this);
        this.relayEvents(this.rangePicker, ["select"]);
    },

    // private
    onBeforeShow: function(){
        if (this.rangePicker){
            this.rangePicker.startDatePicker.hideMonthPicker(true);
            this.rangePicker.endDatePicker.hideMonthPicker(true);
        }
    }
});
Ext.reg('daterangemenu', Genapp.form.menu.DateRangeMenu);