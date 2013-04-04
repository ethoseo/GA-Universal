function indexItems( container, repeat) {
	var $ = jQuery;
	var patterns = ["name", "id"];
	$(container + " " + repeat).each(function (index) {
		$(this).find(":input").each(function () {
			for (var i = patterns.length - 1; i >= 0; i--) {
				if( $(this).attr("data-pattern-" + patterns[i])){
					$(this).attr(patterns[i], $(this).attr("data-pattern-" + patterns[i]).replace("++",index) );
				}
			}
		});
	});
}
function repeater( container, repeat) {
	var $ = jQuery;
	$(container + " " + repeat + ":eq(0) .btnRemove").hide();
	indexItems(container, repeat);
	$(container + " " + repeat + ":visible .btnAdd").live("click", function () {
		var clone = $(this).closest(repeat).clone();
		var $clone = $(clone);
		$clone.find(":input").removeAttr("selected").removeAttr("checked").filter(":not([type=checkbox],[type=radio],[type=select],[type=option])").val("");
		$clone.find(" .btnRemove").show();
		$clone.appendTo(container);
		indexItems(container, repeat);
	});
	$(container + " " + repeat + ":visible .btnRemove").live("click", function () {
		$(this).closest(repeat).remove();
		indexItems(container, repeat);
	});
}