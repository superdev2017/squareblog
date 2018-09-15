var tour = {
  guide_intro: function(name, concept) {
    swal({
      title: 'Nice to meet you ' + name + ' !',
      text: 'Thank you for choosing ' + concept + '! I am Maria, i am here to welcome you to ' + concept + ', and take you on a small tour of our silver website builder.',
      imageUrl: 'https://i.imgur.com/QbFoOzT.jpg',
      imageWidth: 200,
      /*imageHeight: 300,*/
      animation: true,
      confirmButtonColor: '#73A324'
    }).finally(function() {
      introJs().start();
    });
  }
};