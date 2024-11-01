var yay = (function($, yay) {
    function addRule(sel, prop, val) {
        var i, ss, rules, j, len;

        for (i = 0; i < document.styleSheets.length; i++) {
            ss = document.styleSheets[i];           

            try {

                if (ss.cssRules !== undefined) {
                    rules = ss.cssRules;
                } else if (ss.rules !== undefined) {
                    rules = ss.rules;
                } else {
                    rules = [];
                }
            }
            catch (e) {
                rules = [];
            }
            if (rules != undefined) {
                for (j = 0, len = rules.length; j < len; j++) {

                    if (rules[j].selectorText && rules[j].selectorText === sel) {
                        if (val != null) {
                            if (rules[j].style[prop] == '')
                                continue;
                            rules[j].style[prop] = val;


                            return;
                        }
                        else {
                            if (ss.deleteRule) {
                                ss.deleteRule(j);
                            }
                            else if (ss.removeRule) {
                                ss.removeRule(j);
                            }
                            else {
                                rules[j].style.cssText = '';
                            }
                        }
                    }
                }
            }
        }

        ss = document.styleSheets[0] || {};
        if (ss.insertRule) {
            if (ss.hasOwnProperty('cssRules')) {
                rules = ss.cssRules;
            } else if (ss.hasOwnProperty('rules')) {
                rules = ss.rules;
            } else {
                rules = [];
            }
            // ss.insertRule(sel + '{ ' + prop + ':' + val + '; }', rules.length);
        }
        else if (ss.addRule) {
            ss.addRule(sel, prop + ':' + val + ';', 0);
        }
    }

    yay.css = {
        'addRule': addRule
    };
    return yay;
})(jQuery, yay || {});