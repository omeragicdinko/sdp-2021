$(document).ready(function() {

  $("main#spapp > section").height($(document).height() - 60);

  var app = $.spapp({pageNotFound : 'error_404'}); // initialize

  // define routes
  app.route({
    view: 'workers',
    load: 'workers.html'
  });
  app.route({
    view: 'car_list',
    load: 'car_list.html'
  });
  app.route({
    view: 'reservations',
    load: 'reservations.html'
  });
  app.route({
    view: 'comments',
    load: 'comments.html'
  });
  app.route({
    view: 'users',
    load: 'users.html'
  });
  app.route({
    view: 'bases',
    load: 'bases.html'
  });
  app.route({
    view: 'about',
    load: 'about.html'
  });
  app.route({
    view: 'profile',
    load: 'profile.html'
  });

  // run app
  app.run();

});
