
	// JAVASCRIPT CODES VON:
	// http://www.developers-guide.net/c/119-alle-checkboxen-auf-einmal-de-markieren.html


	function mark(check,praefix) {
        var fields = document.forms["actions"].elements;
        for(i=0;i<fields.length;i++) {
                var field = fields[i];
                if((field.name.substr(0,praefix.length) == praefix) && (field.type == 'checkbox')) {
                        field.checked = check;
                }
        }
	}

	function test(feld,praefix) {
        var allchecked = true;
        var fields = document.forms["actions"].elements;
        for(i=0;i<fields.length;i++) {
                var field = fields[i];
                if((field.name.substr(0,praefix.length) == praefix) && (field.type == 'checkbox')) {
                        if(!field.checked) {
                                allchecked = false;
                        }
                }
        }
        document.getElementById(feld).checked = allchecked;
	}