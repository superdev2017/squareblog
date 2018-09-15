$(document).ready(function () {

  $('.gallery').featherlightGallery();

  $('a[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
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
          window.location.href = Routing.generate('fos_user_security_logout');
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

function advancedBuilderNotAuthorized() {
  swal({
    title: 'Upgrade your plan',
    text: 'You do not have access to the gold builder. Please contact support to upgrade your plan.',
    type: "error",
    confirmButtonText: "Ok"
  });
  return false;
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

$(document).ready(function() {

  //Smooth Scroll
  $(function() {
    $('a[href*="#"]:not([href="#"])').click(function() {
      if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          $('html, body').animate({
            scrollTop: target.offset().top
          }, 1000);
          return false;
        }
      }
    });
  });

});