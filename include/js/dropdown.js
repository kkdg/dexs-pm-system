/*
 *		DROPDOWN RECIPIENT-FIELD
 *		Coded by: SamBrishes (PYTES)
 *
 *		My knowledge about JavaScript are unfortunately low.
 *		But .... it works ... ^.^
 */
		
	function get_recipient(value){
		var therecipient = value;
		var splitid = therecipient.split(",");
		
		/* CREATE RECIPIENT ENTRY */
		var input = document.createElement("LI");
		var name = document.createTextNode(splitid[1]);
		var closebutton = document.createElement("a");
		input.appendChild(name);
		input.appendChild(closebutton);
		input.setAttribute("class", "bit-box");
		input.setAttribute("id", "user_" + splitid[0]);
		closebutton.setAttribute("class", "closebutton");
		closebutton.setAttribute("onClick", "remove_recipient('" + splitid[0] + "_-_" + splitid[1] + "')");
		document.getElementById("the_fields").appendChild(input);	
		
		/* CREATE HIDDEN INPUT 4 POST */
		var forminput = document.createElement("INPUT");
		forminput.setAttribute("type", "hidden");
		forminput.setAttribute("name", "recipients[]");
		forminput.setAttribute("value", splitid[0]);
		forminput.setAttribute("id", "theuser_" + splitid[0]);
		document.getElementById("fields").appendChild(forminput);
		
		/* DISABLE RECIPIENT */
		document.getElementById(splitid[0]).disabled = "disabled";
		
		/* JUMP TO FIRST OPTION */										
		document.getElementById('nothing').selected = "selected";
	}
	
	function remove_recipient(id){
		var delrecipient = id;
		var splitme = delrecipient.split("_-_");
		
		/* REMOVE RECIPIENT ENTRY */
		var delli = document.getElementById('user_' + splitme[0]);
		var remElement = (delli.parentNode).removeChild(delli);
		
		/* ENABLE RECIPIENT */
		document.getElementById(splitme[0]).disabled = "";
		
		/* DELETE HIDDEN FORMFIELD */
		var deluser = document.getElementById('theuser_' + splitme[0]);
		var remElement = (deluser.parentNode).removeChild(deluser);
	}