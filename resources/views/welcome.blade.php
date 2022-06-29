<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <title>Telixcel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Telixcel by MGI" />
    <meta name="keywords" content="Chat Templating CMS" />
    <meta name="author" content="Gusindra" />
    <link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link rel="stylesheet" href="https://telixcel.s3.ap-southeast-1.amazonaws.com/frontend/all.min.css?v=26112021">

  </head>
  <body data-spy="scroll" data-target="#ftco-navbar" data-offset="200">

    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
      <div class="container">
        <a class="navbar-brand" href="/">
            <img class="logo-white" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/imgs/logo-white-200.png" title="Telixcel" >
            <img class="logo" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/imgs/logo-200.png" title="Telixcel" >
        </a>
        <button style="display: flex;" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
          <svg style="height: 20px;width: 20px;" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <div style="margin-top: 2px;">Menu</div>
        </button>

        <div class="collapse navbar-collapse" id="ftco-nav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active"><a href="#section-home" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="#section-features" class="nav-link">Features</a></li>
            <!-- <li class="nav-item"><a href="#section-pricing" class="nav-link">Pricing</a></li> -->
            <li class="nav-item"><a href="#section-contact" class="nav-link">Contact</a></li>
            <li class="nav-item"><a href="#section-about" class="nav-link">About</a></li>
            <li class="nav-item"><a href="#section-counter" class="nav-link">Services</a></li>
            @if(auth()->user())
            <li class="nav-item"><a href="{{url('/dashboard')}}" class="btn btn-sm btn-outline-white" style="margin-top: 23px;">Dashboard</a></li>
            @else
            <li class="nav-item"><a href="{{url('/login')}}" class="nav-link">Login</a></li>
            @endif
          </ul>
        </div>
      </div>
    </nav>
    <!-- END nav -->

    <section class="ftco-cover ftco-slant" style="background-image: url(https://telixcel.s3.ap-southeast-1.amazonaws.com/imgs/bg_5.jpg);" id="section-home">
      <div class="container">
        <div class="row align-items-center justify-content-center text-center ftco-vh-100">

          <div class="col-md-12">
            <h1 class="title-text-2" style="display: none;">REACH OUT TO YOUR CUSTOMERS GLOBALLY ON THEIR PREFERRED CONVERSATION CHANNEL.</h1>
            <h1 class="title-text-1" style="display: none;">THE BEST TOOLS FOR INSTANT REACH TO BILLION WHATSAPP USERS.</h1>
            <h1  id="aksen" class="ftco-heading ftco-animate text-right"></h1>
            <!-- <h2 class="h5 ftco-subheading mb-5 ftco-animate">A free template by <a href="#">Free-Template.co</a></h2> -->
            <br><br><br>
            <p class="text-right"><a href="/register" class="btn btn-primary ftco-animate text-center">Get Started</a></p>
          </div>
        </div>
      </div>
    </section>

    <section class="ftco-section bg-light  ftco-slant ftco-slant-white" id="section-features">
      <div class="container">

        <div class="row">
          <div class="col-md-12 text-center mb-5 ftco-animate">
            <h2 class="text-uppercase ftco-uppercase">How it works </h2>
            <!-- <div class="row justify-content-center">
              <div class="col-md-7">
                <p class="lead">Reach out to your customers globally on their preferred conversation channel across digital devices.</p>
              </div>
            </div> -->
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
              <div class="ftco-icon mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
              </div>
              <div class="media-body">
                <h5 class="mt-0">Notification</h5>
                <small class="mb-5">Alert, Reminders, Ticketing, Digital receipts, Delivery locations.</small>
                <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
              <div class="ftco-icon mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
              </div>
              <div class="media-body">
                <h5 class="mt-0">Customer Support</h5>
                <small class="mb-5">Live chat support, Chat commerce.</small>
                <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
              <div class="ftco-icon mb-3">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              </div>
              <div class="media-body">
                <h5 class="mt-0">Authentication</h5>
                <small class="mb-5">Second Factor of Authentication (2FA).</small>
                <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
              <div class="ftco-icon mb-3">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              </div>
              <div class="media-body">
                <h5 class="mt-0">Educational</h5>
                <small class="mb-5">Videos, Quizzes, Live chat tutorials.</small>
                <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
              <div class="ftco-icon mb-3">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
              <div class="media-body">
                <h5 class="mt-0">Entertainment</h5>
                <small class="mb-5">Images, Video trailers, Audio files.</small>
                <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
              <div class="ftco-icon mb-3">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
              </div>
              <div class="media-body">
                <h5 class="mt-0">Enterprise</h5>
                <small class="mb-5">Information and Documentation sharing.</small>
                <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
              <div class="ftco-icon mb-3">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3h5m0 0v5m0-5l-6 6M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z"></path></svg>
              </div>
              <div class="media-body">
                <h5 class="mt-0">Emergency Service</h5>
                <small class="mb-5">Share real time location.</small>
                <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
              <div class="ftco-icon mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path></svg>
              </div>
              <div class="media-body">
                <h5 class="mt-0">Customization</h5>
                <small class="mb-5">Other needs.</small>
                <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- END section -->

    <!-- <section class="ftco-section ftco-slant" id="section-services">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center ftco-animate">
            <h2 class="text-uppercase ftco-uppercase">Services</h2>
            <div class="row justify-content-center mb-5">
              <div class="col-md-7">
                <p class="lead">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-4 mb-5 ftco-animate">
            <figure><img src="images/img_2.jpg" alt="Free Template by Free-Template.co" class="img-fluid"></figure>
            <div class="p-3">
              <h3 class="h4">Title of Service here</h3>
              <p class="mb-4">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
              <ul class="list-unstyled ftco-list-check text-left">
                <li class="d-flex mb-2"><span class="oi oi-check mr-3 text-primary"></span> <span>Free template for designer and developers</span></li>
                <li class="d-flex mb-2"><span class="oi oi-check mr-3 text-primary"></span> <span>Vokalia and consonantia blind texts</span></li>
                <li class="d-flex mb-2"><span class="oi oi-check mr-3 text-primary"></span> <span>Behind the word mountains blind texts</span></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-4 mb-5 ftco-animate">
            <figure><img src="images/img_1.jpg" alt="Free Template by Free-Template.co" class="img-fluid"></figure>
            <div class="p-3">
              <h3 class="h4">Title of Service here</h3>
              <p class="mb-4">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
              <ul class="list-unstyled ftco-list-check text-left">
                <li class="d-flex mb-2"><span class="oi oi-check mr-3 text-primary"></span> <span>Free template for designer and developers</span></li>
                <li class="d-flex mb-2"><span class="oi oi-check mr-3 text-primary"></span> <span>Vokalia and consonantia blind texts</span></li>
                <li class="d-flex mb-2"><span class="oi oi-check mr-3 text-primary"></span> <span>Behind the word mountains blind texts</span></li>
              </ul>
            </div>
          </div>

          <div class="col-lg-4 mb-5 ftco-animate">
            <figure><img src="images/img_3.jpg" alt="Free Template by Free-Template.co" class="img-fluid"></figure>
            <div class="p-3">
              <h3 class="h4">Title of Service here</h3>
              <p class="mb-4">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
              <ul class="list-unstyled ftco-list-check text-left">
                <li class="d-flex mb-2"><span class="oi oi-check mr-3 text-primary"></span> <span>Free template for designer and developers</span></li>
                <li class="d-flex mb-2"><span class="oi oi-check mr-3 text-primary"></span> <span>Vokalia and consonantia blind texts</span></li>
                <li class="d-flex mb-2"><span class="oi oi-check mr-3 text-primary"></span> <span>Behind the word mountains blind texts</span></li>
              </ul>
            </div>
          </div>

        </div>
      </div>
    </section> -->

    <!-- PRICEING -->
    <!-- <section class="ftco-section bg-light ftco-slant ftco-slant-white" id="section-pricing">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center ftco-animate">
            <h2 class="text-uppercase ftco-uppercase">Pricing</h2>
            <div class="row justify-content-center mb-5">
              <div class="col-md-7">
                <p class="lead">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md bg-white p-5 m-2 text-center mb-2 ftco-animate">
            <div class="ftco-pricing">
              <h2>Standard</h2>
              <p class="ftco-price-per text-center"><sup>$</sup><strong>25</strong><span>/mo</span></p>
              <ul class="list-unstyled mb-5">
                <li>Far far away behind the word mountains</li>
                <li>Even the all-powerful Pointing has no control</li>
                <li>When she reached the first hills</li>
              </ul>
              <p><a href="#" class="btn btn-secondary btn-sm">Get Started</a></p>
            </div>
          </div>
          <div class="col-md bg-white p-5 m-2 text-center mb-2 ftco-animate">
            <div class="ftco-pricing">
              <h2>Professional</h2>
              <p class="ftco-price-per text-center"><sup>$</sup><strong>75</strong><span>/mo</span></p>
              <ul class="list-unstyled mb-5">
                <li>Far far away behind the word mountains</li>
                <li>Even the all-powerful Pointing has no control</li>
                <li>When she reached the first hills</li>
              </ul>
              <p><a href="#" class="btn btn-secondary btn-sm">Get Started</a></p>
            </div>
          </div>
          <div class="w-100 clearfix d-xl-none"></div>
          <div class="col-md bg-white  ftco-pricing-popular p-5 m-2 text-center mb-2 ftco-animate">
            <span class="popular-text">Popular</span>
            <div class="ftco-pricing">
              <h2>Silver</h2>
              <p class="ftco-price-per text-center"><sup>$</sup><strong class="text-primary">135</strong><span>/mo</span></p>
              <ul class="list-unstyled mb-5">
                <li>Far far away behind the word mountains</li>
                <li>Even the all-powerful Pointing has no control</li>
                <li>When she reached the first hills</li>
              </ul>
              <p><a href="#" class="btn btn-primary btn-sm">Get Started</a></p>
            </div>
          </div>
          <div class="col-md bg-white p-5 m-2 text-center mb-2 ftco-animate">
            <div class="ftco-pricing">
              <h2>Platinum</h2>
              <p class="ftco-price-per text-center"><sup>$</sup><strong>215</strong><span>/mo</span></p>
              <ul class="list-unstyled mb-5">
                <li>Far far away behind the word mountains</li>
                <li>Even the all-powerful Pointing has no control</li>
                <li>When she reached the first hills</li>
              </ul>
              <p><a href="#" class="btn btn-secondary btn-sm">Get Started</a></p>
            </div>
          </div>
        </div>
      </div>
    </section> -->

    <!-- <section class="ftco-section ftco-slant ftco-slant-light">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center ftco-animate">
            <h2 class="text-uppercase ftco-uppercase">More Features</h2>
            <div class="row justify-content-center mb-5">
              <div class="col-md-7">
                <p class="lead">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="owl-carousel ftco-owl">

              <div class="item ftco-animate">
                <div class="media d-block text-left ftco-media p-md-5 p-4">
                  <div class="ftco-icon mb-3"><span class="oi oi-pencil display-4"></span></div>
                  <div class="media-body">
                    <h5 class="mt-0">Easy to Customize</h5>
                    <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
                  </div>
                </div>
              </div>

              <div class="item ftco-animate">
                <div class="media d-block text-left ftco-media p-md-5 p-4">
                  <div class="ftco-icon mb-3"><span class="oi oi-monitor display-4"></span></div>
                  <div class="media-body">
                    <h5 class="mt-0">Web Development</h5>
                    <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
                  </div>
                </div>
              </div>

              <div class="item ftco-animate">
                <div class="media d-block text-left ftco-media p-md-5 p-4">
                  <div class="ftco-icon mb-3"><span class="oi oi-location display-4"></span></div>
                  <div class="media-body">
                    <h5 class="mt-0">Free Bootstrap 4</h5>
                    <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
                  </div>
                </div>
              </div>

              <div class="item ftco-animate">
                <div class="media d-block text-left ftco-media p-md-5 p-4">
                  <div class="ftco-icon mb-3"><span class="oi oi-person display-4"></span></div>
                  <div class="media-body">
                    <h5 class="mt-0">For People Like You</h5>
                    <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section> -->

    <!-- <section class="ftco-section ftco-slant ftco-slant-light  bg-light ftco-slant ftco-slant-white" id="section-faq">
      <div class="container">
        <div class="row mb-5">
          <div class="col-md-12 text-center ftco-animate">
            <h2 class="text-uppercase ftco-uppercase">FAQ</h2>
            <div class="row justify-content-center mb-5">
              <div class="col-md-7">
                <p class="lead">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-5 ftco-animate">
            <h3 class="h5 mb-4">What is {{ env('APP_NAME')}}?</h3>
            <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
            <small class="mb-5"><a href="#">Learn More</a></p>
          </div>
          <div class="col-md-6 mb-5 ftco-animate">
            <h3 class="h5 mb-4">Can I upgrade?</h3>
            <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
            <small class="mb-5"><a href="#">Learn More</a></p>
          </div>
          <div class="col-md-6 mb-5 ftco-animate">
            <h3 class="h5 mb-4">Can I have more than 5 users?</h3>
            <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
            <small class="mb-5"><a href="#">Learn More</a></p>
          </div>
          <div class="col-md-6 mb-5 ftco-animate">
            <h3 class="h5 mb-4">If I need support who do I contact?</h3>
            <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
            <small class="mb-5"><a href="#">Learn More</a></p>
          </div>
        </div>
      </div>
    </section> -->

    <section class="ftco-section bg-white ftco-slant" id="section-contact">
      <div class="container">
        <div class="row">

        <div class="col-md-3 text-center ftco-animate">
            <h2 class="text-uppercase text-left ftco-uppercase">Support</h2>
          </div>
          <div class="col-md-9 pr-md-5 mb-5 ftco-animate">
            <form>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="sr-only">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Name" onchange="myName(this.value)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="sr-only">Phone</label>
                            <input type="text" class="form-control" id="email" placeholder="Phone" onchange="myContact(this.value)">
                        </div>
                    </div>
                </div>
              <div class="form-group">
                <label for="message" class="sr-only">Message</label>
                <textarea name="message" id="message" cols="30" rows="10" class="form-control" placeholder="Write your message" onchange="myMessage(this.value)"></textarea>
              </div>
              <div class="form-group">
                <a id="contact_support" class="btn btn-primary" href="mailto:support@telixcel.com?subject=Support%20Telixcel%20from%20Website&body=Name:%0d%0aPhone:%0d%0aMessage:%0d%0a">
                    Send
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <section class="ftco-section bg-light ftco-slant ftco-slant-white" id="section-about">
      <div class="container">

        <div class="row mb-5">
          <div class="col-md-3 text-left ftco-animate">
            <h2 class="text-uppercase ftco-uppercase">About Us</h2>
          </div>
          <div class="col-md-9 text-center ftco-animate">
            <div class="rext-right mb-5">
              <div class="text-left">
                <p class="lead">Telixcel is a Communication Bussiness Solution provider founded in 2018 based in Indonesia. </p>

Vision
<ul>
    <li>To be the most trusted partner in providing reliable business solutions that adds value to our customers.</li>
</ul>

Mission<br>
<ul>
    <li>To provide total solution of Customer Experience Management</li>
    <li>To add values to client's business</li>
    <li>To bring tangible benefits to clients</li>
    <li>To be preferred partner and good place to work</li>
</ul>
Feel free to send us an email to <a href="mailto:support@telixcel.com">support@telixcel.com </a>
              </div>
            </div>
          </div>
        </div>
        <!-- END row -->

<!--
        <div class="row no-gutters align-items-center ftco-animate">
          <div class="col-md-6 mb-md-0 mb-5">
            <img src="images/bg_3.jpg" alt="Free Template by Free-Template.co" class="img-fluid">
          </div>
          <div class="col-md-6 p-md-5">
            <h3 class="h3 mb-4">Far far away, behind the word mountains</h3>
            <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
            <small class="mb-5"><a href="#">Learn More</a></p>
          </div>
        </div>
        <div class="row no-gutters align-items-center ftco-animate">
          <div class="col-md-6 order-md-3 mb-md-0 mb-5">
            <img src="images/bg_1.jpg" alt="Free Template by Free-Template.co" class="img-fluid">
          </div>
          <div class="col-md-6 p-md-5 order-md-1">
            <h3 class="h3 mb-4">Far from the countries Vokalia and Consonantia</h3>
            <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
            <small class="mb-5"><a href="#">Learn More</a></p>
          </div>
        </div> -->

      </div>
    </section>

    <section class="ftco-section bg-white ftco-slant ftco-slant-dark" id="section-counter">
      <div class="container">
        <div class="row mb-5">
          <div class="col-md-3 text-center ftco-animate">
            <h5 class="text-uppercase ftco-uppercase">The Best tool for</h5>
          </div>
          <div class="col-md-9 text-center ftco-animate">
            <div class="row justify-content-center mb-5">
              <div class="col-md-7" >

                <h1 class="tool-1" style="display: none;">E-Commerce.</h1>
                <h1 class="tool-2" style="display: none;">Customer Service.</h1>
                <h1 id="best"></h1>
              </div>
            </div>
          </div>
        </div>
        <!-- END row -->
        <!-- <div class="row">
          <div class="col-md ftco-animate">
            <div class="ftco-counter text-center">
              <span class="ftco-number" data-number="34146">0</span>
              <span class="ftco-label">Lines of Codes</span>
            </div>
          </div>
          <div class="col-md ftco-animate">
            <div class="ftco-counter text-center">
              <span class="ftco-number" data-number="1239">0</span>
              <span class="ftco-label">Pizza Consume</span>
            </div>
          </div>
          <div class="col-md ftco-animate">
            <div class="ftco-counter text-center">
              <span class="ftco-number" data-number="124">0</span>
              <span class="ftco-label">Number of Clients</span>
            </div>
          </div>
        </div>
      </div> -->

    </section>



    <footer class="ftco-footer ftco-bg-dark">
      <div class="container">
        <div class="row mb-5">
          <div class="col-md-8">
            <div class="row">
              <div class="col-md text-center">
                <div class="ftco-footer-widget mb-4">
                  <h2 class="ftco-heading-2">Our Company</h2>
                  <ul class="list-unstyled">
                    <li><a href="#" class="py-2 d-block">Who we are</a></li>
                    <li><a href="#" class="py-2 d-block">Carrer</a></li>
                    <li><a href="#" class="py-2 d-block">Polices</a></li>
                    <li><a href="#" class="py-2 d-block">Hero</a></li>
                    <li><a href="#" class="py-2 d-block">Stay Safe</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md text-center">
                 <div class="ftco-footer-widget mb-4">
                  <h2 class="ftco-heading-2">Our Contact</h2>
                  <ul class="list-unstyled">
                    <li><a href="mailto:support@telixcel.com" class="py-2 d-block">Support</a></li>
                    <li><a href="https://goo.gl/maps/X67teAF98m9YZFhN7" class="py-2 d-block">Offices</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md text-center">
                 <div class="ftco-footer-widget mb-4">
                  <h2 class="ftco-heading-2">Our Matter</h2>
                  <ul class="list-unstyled">
                    <li><a href="{{ url('/') }}" class="py-2 d-block">Event</a></li>
                    <li><a href="{{ url('/') }}" class="py-2 d-block">News</a></li>

                    @if (Route::has('login'))
                        @auth
                            <li><a href="{{ url('/dashboard') }}" class="py-2 d-block">Account</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="py-2 d-block">Log in</a></li>

                            @if (Route::has('register'))
                                <li><a href="{{ route('register') }}" class="py-2 d-block">Register</a></li>
                            @endif
                        @endauth
                    @endif
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="ftco-footer-widget mb-4 text-center">
                <h2 class="ftco-heading-2 ">Follow us</h2>

                <ul class="ftco-footer-social1 list-unstyled float-md-right1 float-lft" style=" display: flex;align-items: center;justify-content: center;">
                    <li class="px-2"><a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
width="32" height="32"
viewBox="0 0 32 32"
style=" fill:#ffffff;"><path d="M 28 8.558594 C 27.117188 8.949219 26.167969 9.214844 25.171875 9.332031 C 26.1875 8.722656 26.96875 7.757813 27.335938 6.609375 C 26.386719 7.171875 25.332031 7.582031 24.210938 7.804688 C 23.3125 6.847656 22.03125 6.246094 20.617188 6.246094 C 17.898438 6.246094 15.691406 8.453125 15.691406 11.171875 C 15.691406 11.558594 15.734375 11.933594 15.820313 12.292969 C 11.726563 12.089844 8.097656 10.128906 5.671875 7.148438 C 5.246094 7.875 5.003906 8.722656 5.003906 9.625 C 5.003906 11.332031 5.871094 12.839844 7.195313 13.722656 C 6.386719 13.695313 5.628906 13.476563 4.964844 13.105469 C 4.964844 13.128906 4.964844 13.148438 4.964844 13.167969 C 4.964844 15.554688 6.660156 17.546875 8.914063 17.996094 C 8.5 18.109375 8.066406 18.171875 7.617188 18.171875 C 7.300781 18.171875 6.988281 18.140625 6.691406 18.082031 C 7.316406 20.039063 9.136719 21.460938 11.289063 21.503906 C 9.605469 22.824219 7.480469 23.609375 5.175781 23.609375 C 4.777344 23.609375 4.386719 23.585938 4 23.539063 C 6.179688 24.9375 8.765625 25.753906 11.546875 25.753906 C 20.605469 25.753906 25.558594 18.25 25.558594 11.742188 C 25.558594 11.53125 25.550781 11.316406 25.542969 11.105469 C 26.503906 10.410156 27.339844 9.542969 28 8.558594 Z"></path></svg>
                    </a></li>
                    <li class="px-2"><a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
width="32" height="32"
viewBox="0 0 32 32"
style=" fill:#ffffff;"><path d="M 7 5 C 5.90625 5 5 5.90625 5 7 L 5 25 C 5 26.09375 5.90625 27 7 27 L 25 27 C 26.09375 27 27 26.09375 27 25 L 27 7 C 27 5.90625 26.09375 5 25 5 Z M 7 7 L 25 7 L 25 25 L 19.8125 25 L 19.8125 18.25 L 22.40625 18.25 L 22.78125 15.25 L 19.8125 15.25 L 19.8125 13.3125 C 19.8125 12.4375 20.027344 11.84375 21.28125 11.84375 L 22.90625 11.84375 L 22.90625 9.125 C 22.628906 9.089844 21.667969 9.03125 20.5625 9.03125 C 18.257813 9.03125 16.6875 10.417969 16.6875 13 L 16.6875 15.25 L 14.0625 15.25 L 14.0625 18.25 L 16.6875 18.25 L 16.6875 25 L 7 25 Z"></path></svg>
                    </a></li>
                    <li class="px-2"><a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
width="32" height="32"
viewBox="0 0 32 32"
style=" fill:#ffffff;"><path d="M 11.46875 5 C 7.917969 5 5 7.914063 5 11.46875 L 5 20.53125 C 5 24.082031 7.914063 27 11.46875 27 L 20.53125 27 C 24.082031 27 27 24.085938 27 20.53125 L 27 11.46875 C 27 7.917969 24.085938 5 20.53125 5 Z M 11.46875 7 L 20.53125 7 C 23.003906 7 25 8.996094 25 11.46875 L 25 20.53125 C 25 23.003906 23.003906 25 20.53125 25 L 11.46875 25 C 8.996094 25 7 23.003906 7 20.53125 L 7 11.46875 C 7 8.996094 8.996094 7 11.46875 7 Z M 21.90625 9.1875 C 21.402344 9.1875 21 9.589844 21 10.09375 C 21 10.597656 21.402344 11 21.90625 11 C 22.410156 11 22.8125 10.597656 22.8125 10.09375 C 22.8125 9.589844 22.410156 9.1875 21.90625 9.1875 Z M 16 10 C 12.699219 10 10 12.699219 10 16 C 10 19.300781 12.699219 22 16 22 C 19.300781 22 22 19.300781 22 16 C 22 12.699219 19.300781 10 16 10 Z M 16 12 C 18.222656 12 20 13.777344 20 16 C 20 18.222656 18.222656 20 16 20 C 13.777344 20 12 18.222656 12 16 C 12 13.777344 13.777344 12 16 12 Z"></path></svg>
                    </a></li>
                </ul>
                <h1 class="ftco-heading-21 text-white navbar-brand"><img src="https://telixcel.s3.ap-southeast-1.amazonaws.com/imgs/logo-white-200.png" title="{{ env('APP_NAME')}}" style="width: 200px;-webkit-filter: brightness(1000%); /* Safari 6.0 - 9.0 */
  filter: brightness(1000%);"/></h2>
                <p>&copy; {{ env('APP_NAME')}} {{date('Y')}}. All Rights Reserved.</p>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#4586ff"/></svg></div>


    <script src="https://telixcel.s3.ap-southeast-1.amazonaws.com/frontend/all.js"></script>
    <script src="frontend/js/typeit.js"></script>
    <script>
        var app = document.getElementById('aksen');

        var typewriter1 = new TypeIt(app, {
            loop: true,
            strings: [$('.title-text-1').text(),$('.title-text-2').text()],
            speed: 85,
            nextStringDelay: [1000,5000],
            startDelay: 1000,
            breakLines: false,
            loopDelay: false,
            cursor: true,
	    });

        var tool = document.getElementById('best');

        var typewriter2 = new TypeIt(tool, {
            loop: true,
            strings: [$('.tool-1').text(),$('.tool-2').text()],
            speed: 85,
            nextStringDelay: [1000,5000],
            startDelay: 1000,
            breakLines: false,
            loopDelay: false,
            cursor: true,
	    });

        function myName(val) {
            var url = $('#contact_support').attr('href');
            var new_url = url.replace("Name:", "Name: "+val);
            $('#contact_support').attr('href', new_url);

        }
        function myContact(val) {
            var url = $('#contact_support').attr('href');
            var new_url = url.replace("Phone:", "Phone: "+val);
            $('#contact_support').attr('href', new_url);
        }
        function myMessage(val) {
            var url = $('#contact_support').attr('href');
            var new_url = url.replace("Message:", "Message: "+val);
            $('#contact_support').attr('href', new_url);
        }
    </script>
  </body>
</html>
