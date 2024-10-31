tinyMCEPopup.requireLangPack();

var RevealerDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		f.connector.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.style.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.fold_top.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.fold_bottom.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});

		f.position.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.left.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.top.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});

		f.fixed_pos.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.fixed_left.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.fixed_top.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.fixed_right.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.fixed_bottom.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});

		f.width.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.height.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.background_color.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.padding.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.border_color.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.border_width.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.radius.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});

		f.add_arrow.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.arrow.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.arrow_color.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
	},

	insert : function() {
		// Insert the contents from the input into the document
		var f = document.forms[0];
		var s = ' ';
		var t0 = '="';
		var t1 = '" ';
		var begin = '[revealer ';
		var end = '[/revealer]';
		var sParam = '';
		var tParam = '';
		
		var sVal = ["switch"];
		var sAtt = ["role"];
		var tVal = ["highlight"]
		var tAtt = ["role"];

		var arrow, position, fixed_pos;

		// Get the selected contents as text and place it in the input
		sVal.push(f.connector.value);
		tVal.push(f.connector.value);
		sAtt.push("connector");
		tAtt.push("connector");

		sVal.push(f.style.value);
		sAtt.push("style");

		sVal.push(f.fold_top.value);
		sAtt.push("fold_top");

		sVal.push(f.fold_bottom.value);
		sAtt.push("fold_bottom");
		
		tVal.push(f.width.value);
		tAtt.push("width");

		tVal.push(f.height.value);
		tAtt.push("height");

		tVal.push(f.background_color.value);
		tAtt.push("background_color");

		tVal.push(f.padding.value);
		tAtt.push("padding");

		tVal.push(f.position.value);
		tAtt.push("position");
		position = f.position.value;
		
		if ((position == 'center') || (position == 'left') || (position == 'right')) {
			tVal.push(f.left.value);
			tAtt.push("left");
	
			tVal.push(f.top.value);
			tAtt.push("top");

		} else if (position == 'fixed') {
			tVal.push(f.fixed_pos.value);
			tAtt.push("fixed_pos");
			
			fixed_pos = f.fixed_pos.value;
			
			if (fixed_pos == 'custom') {
				tVal.push(f.fixed_top.value);
				tAtt.push("fixed_top");
	
				tVal.push(f.fixed_right.value);
				tAtt.push("fixed_right");
	
				tVal.push(f.fixed_bottom.value);
				tAtt.push("fixed_bottom");
	
				tVal.push(f.fixed_left.value);
				tAtt.push("fixed_left");
			}
		} 
	
		tVal.push(f.border_color.value);
		tAtt.push("border_color");

		tVal.push(f.border_width.value);
		tAtt.push("border_width");

		tVal.push(f.radius.value);
		tAtt.push("radius");

		arrow = f.add_arrow.value;
		if (arrow == 'yes') {
			tVal.push(f.arrow.value);
			tAtt.push("arrow");

			tVal.push(f.arrow_color.value);
			tAtt.push("arrow_color");
		}

		for(i=0; i<sVal.length; i++) {
			if (sVal[i] != "") sParam += s + sAtt[i] + t0 + sVal[i] + t1;
		}

		for(i=0; i<tVal.length; i++) {
			if (tVal[i] != "") tParam += s + tAtt[i] + t0 + tVal[i] + t1;
		}

		var switchCode = begin + sParam + ']Add Your Switch Here' + end;
		var targetCode = begin + tParam + ']Add Your Highlight Here' + end;

		var shortcode = switchCode + targetCode;

		tinyMCEPopup.editor.execCommand('mceInsertContent', false, shortcode);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(RevealerDialog.init, RevealerDialog);