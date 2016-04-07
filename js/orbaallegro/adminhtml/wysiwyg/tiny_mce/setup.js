(function(){
    if(typeof tinyMceWysiwygSetup != "undefinded"){
        var _oldGetSettings = tinyMceWysiwygSetup.prototype.getSettings;
        tinyMceWysiwygSetup.prototype.getSettings = function(mode){
            var ret = _oldGetSettings.apply(this, arguments);
            ret.valid_children = "+body[style]";
            return ret;
        }
    }
})();

