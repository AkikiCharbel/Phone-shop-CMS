let v = 1.4;

$(document).ready(function() {
    getMoneyLeft();
});

function getTotalPrice(){
    var repeatable = $("div[data-repeatable-holder='soled_phones']");
    var repeatableRow = repeatable.find("input[data-repeatable-input-name='price_sold']");
    var totalAmount = $("input[name='amount']")

    var totalPrice = 0;
    repeatableRow.each(function(){
        totalPrice+=parseInt($(this).val());
    });
    totalAmount.val(totalPrice);
}

function getMoneyLeft(){
    var totalPricePaid = 0;
    setTimeout(function() {
        var moneyLeft = $("input[name='amount_left']");
        var repeatable = $("div[data-repeatable-holder='selloutPayments']");
        var repeatableRow = repeatable.find("input[data-repeatable-input-name='amount']");

        repeatableRow.each(function(){
            totalPricePaid+=parseInt($(this).val());
        });

        moneyLeft.val($("input[name='amount']").val() - totalPricePaid);
    }, 200);
}
