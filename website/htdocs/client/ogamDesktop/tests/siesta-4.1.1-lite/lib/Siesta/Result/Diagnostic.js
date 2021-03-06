/*

Siesta 4.1.1
Copyright(c) 2009-2016 Bryntum AB
http://bryntum.com/contact
http://bryntum.com/products/siesta/license

*/
Class('Siesta.Result.Diagnostic', {
    
    isa : Siesta.Result,
    
    has : {
        isWarning           : false
    },

    methods : {
        
        toString : function () {
            return '# ' + this.description
        },
        
        
        toJSON : function () {
            var info        = {
                parentId        : this.parent.id,
                
                type            : this.meta.name,
                description     : this.description
            }
            
            if (this.isWarning) info.isWarning = true
            
            return info
        }
    }    
});

