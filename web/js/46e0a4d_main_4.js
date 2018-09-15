$(document).ready(function () {

  if (readCookie('returning_visitor') != 1) {
    window.sr = new scrollReveal({ mobile: false });
    createCookie('returning_visitor', 1, 365);
  }

  jQuery(function(){
    jQuery("a.bla-1").YouTubePopUp();
  });

  $('.carousel').carousel({
    pause: 'none'
  });
  
  var owl = $("#owl-main");
  owl.owlCarousel({
    itemsCustom : [
      [0, 1],
      [450, 1],
      [600, 1],
      [700, 1],
      [1000, 1],
    ],
    navigation : true,
    autoPlay : 6000,
    slideSpeed: 3000,
    stopOnHover : true

  });

  // Accordion toggle
  $(".accordion-tab").click(function(t) {
    var e = $(this).closest(".accordion-wrapper");
    $(".accordion-content", e).fadeToggle();
    var n = $(".accordion-chevron", this);
    $(".down", n).toggle(), $(".up", n).toggle()
  });


  $( document ).on( "click", ".cancel-subscription", function() {
    var id = $(this).attr('data-subscription-id');

    var text = translations['you.will.get.%product_name%.on.%next_billing_date%.if.you.cancel.you.will.not.be.able.to.recover.your.account'];
    text = text.replace("%product_name%", $('#product_name').text());
    text = text.replace("%next_billing_date%", $('#next-billing-date').text());

    swal({
      title: translations['are.you.sure?'],
      text: text,
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: translations['yes'],
      cancelButtonText: translations['no']
    }).then(function() {
      cancelSubscription(id);
    });
  });

});

function cancelSubscription() {
  $.ajax({
    url: Routing.generate('api_1.cancel_subscription'),
    type: "POST",
    data: [],
    success: function (result) {
      result = JSON.parse(result);
      if (result.status == 'success') {
        swal({
          title: translations['success'],
          text: translations['your.subscription.has.been.canceled'],
          type: 'success'
        }).then(function() {
          window.location.href = Routing.generate('customer_logout');
        });
      } else {
        swal({
          title: translations['error'],
          text: result.message,
          type: "error",
          confirmButtonText: "Ok"
        });
      }
    },
    error: function () {
      swal({
        title: translations['error'],
        text: translations['there.was.an.error.canceling.your.subscription.please.contact.support'],
        type: "error",
        confirmButtonText: "Ok"
      });
    }
  });
}


function createCookie(name,value,days) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else var expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}