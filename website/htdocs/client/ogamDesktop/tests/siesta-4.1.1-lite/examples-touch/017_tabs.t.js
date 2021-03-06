StartTest(function(t) {
    t.diag('Testing Tab Panel');
    
    Ext.create('Ext.TabPanel', {
        fullscreen: true,

        defaults: {
            styleHtmlContent: true
        },

        items: [
            {
                title: 'Home',
                html: 'Home Screen'
            },
            {
                title: 'Contact',
                html: 'Contact Screen'
            },
            {
                title: 'Foo',
                html: 'Foo Screen'
            },
            {
                title: 'Bar',
                html: 'Bar Screen'
            }
        ]
    });

    t.waitForCQ("tabpanel[rendered=true]", function(tabs) {
        var tabPanel    = tabs[0];
        var bar         = tabPanel.getTabBar()
        
        t.willFireNTimes(tabPanel.down('[title=Contact]'), 'activate', 1); 
        t.willFireNTimes(tabPanel.down('[title=Foo]'), 'activate', 1); 
        t.willFireNTimes(tabPanel.down('[title=Bar]'), 'activate', 1); 

        t.chain(
            { tap : bar.items.getAt(0) },
            { waitFor : 'Animations' },
            { tap : bar.items.getAt(1) },
            { waitFor : 'Animations' },
            { tap : bar.items.getAt(2) },
            { waitFor : 'Animations' },
            { tap : bar.items.getAt(3) },

            function() {
                t.pass('Could click all tabs ok');
                t.is(tabPanel.getActiveItem().getHtml(), 'Bar Screen', 'Last tab activated');
            }
        );
    });
});
