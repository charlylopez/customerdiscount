/** JS */
$(document).ready(() => {
	$(".discountform__create").click(function (e) {
		$("#discountform-id_customerdiscount").val("");
		$("#discountform-id_customer").val("");
		$("#discountform-id_category").val("");
		$("#discountform-title").val("");
		$("#discountform-discount").val("");
		$("#discountform-discount_type").val("");

		$("#discountform__table").addClass('hidden');
		$("#discountform").removeClass('hidden');
	})

	$(".discountform__back").click(function (e) {
		$("#discountform__table").removeClass('hidden');
		$("#discountform").addClass('hidden');
	})

	$(".discountform__edit").click(function (e) {
		let row = $(this).closest('tr');
		let id = parseInt(row.data('id'));
		let id_customer = parseInt(row.data('id-customer'));
		let id_category = parseInt(row.data('id-category'));
		let title = row.data('title');
		let discount = parseFloat(row.data('discount'));
		let discount_type = parseInt(row.data('discount_type'));

		$("#discountform-id_customerdiscount").val(id);
		$("#discountform-id_customer").val(id_customer);
		$("#discountform-id_category").val(id_category);
		$("#discountform-title").val(title);
		$("#discountform-discount").val(discount);
		$("#discountform-discount_type").val(discount_type);

		$("#discountform__table").addClass('hidden');
		$("#discountform").removeClass('hidden');
	})
})
