<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!--
    More Templates Visit ==> Free-Template.co
    -->
    <title>Telixcel</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Telixcel Management System by MGI" />
    <meta name="keywords" content="auto bot chat, project asistance" />
    <meta name="author" content="Gusindra MGI" />

    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link rel="stylesheet" href="frontend/css/all.css">

</head>

<body data-spy="scroll" data-target="#ftco-navbar" data-offset="200">

    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img class="logo-white" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/imgs/logo-white-200.png"
                    title="Telixcel">
                <img class="logo" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/imgs/logo-200.png"
                    title="Telixcel">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
                aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
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
                    <li class="nav-item"><a href="{{url('/dashboard')}}" class="nav-link">Dashboard</a></li>
                    @else
                    <li class="nav-item"><a href="{{url('/login')}}" class="nav-link">Login</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <!-- END nav -->

    <section class="ftco-cover ftco-slant" style="background-image: url(frontend/images/bg_5.jpg);" id="section-home">
        <div class="container">
            <div class="row align-items-center justify-content-center text-center ftco-vh-100">

                <div class="col-md-12">
                    <h1 class="title-text-2" style="display: none;">REACH OUT TO YOUR CUSTOMERS GLOBALLY ON THEIR
                        PREFERRED CONVERSATION CHANNEL.</h1>
                    <h1 class="title-text-1" style="display: none;">THE BEST TOOLS FOR INSTANT REACH TO BILLION WHATSAPP
                        USERS.</h1>
                    <h1 id="aksen" class="ftco-heading ftco-animate text-right"></h1>
                    <!-- <h2 class="h5 ftco-subheading mb-5 ftco-animate">A free template by <a href="#">Free-Template.co</a></h2> -->
                    <br><br><br>
                    <p class="text-right"><a href="/register" target="_blank"
                            class="btn btn-primary ftco-animate text-right">Get Started</a></p>
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
                        <div class="ftco-icon mb-3"><span class="oi oi-bell display-4 text-muted"></span></div>
                        <div class="media-body">
                            <h5 class="mt-0">Notification</h5>
                            <small class="mb-5">Alert, Reminders, Ticketing, Digital receipts, Delivery
                                locations.</small>
                            <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
                        <div class="ftco-icon mb-3"><span class="oi oi-chat display-4 text-muted"></span></div>
                        <div class="media-body">
                            <h5 class="mt-0">Customer Support</h5>
                            <small class="mb-5">Live chat support, Chat commerce.</small>
                            <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
                        <div class="ftco-icon mb-3"><span class="oi oi-person display-4 text-muted"></span></div>
                        <div class="media-body">
                            <h5 class="mt-0">Authentication</h5>
                            <small class="mb-5">Second Factor of Authentication (2FA).</small>
                            <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
                        <div class="ftco-icon mb-3"><span class="oi oi-book display-4 text-muted"></span></div>
                        <div class="media-body">
                            <h5 class="mt-0">Educational</h5>
                            <small class="mb-5">Videos, Quizzes, Live chat tutorials.</small>
                            <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
                        <div class="ftco-icon mb-3"><span class="oi oi-media-play display-4 text-muted"></span></div>
                        <div class="media-body">
                            <h5 class="mt-0">Entertainment</h5>
                            <small class="mb-5">Images, Video trailers, Audio files.</small>
                            <!-- <p class="mb-0"><a href="#" class="btn btn-primary btn-sm">Learn More</a></p> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="media d-block mb-4 text-center ftco-media p-md-5 p-4 ftco-animate">
                        <div class="ftco-icon mb-3"><span class="oi oi-cloud-download display-4 text-muted"></span>
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
                        <div class="ftco-icon mb-3"><span class="oi oi-cloud-download display-4 text-muted"></span>
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
                        <div class="ftco-icon mb-3"><span class="oi oi-cloud-download display-4 text-muted"></span>
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
                                    <input type="text" class="form-control" id="name" placeholder="Name"
                                        onchange="myName(this.value)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="sr-only">Phone</label>
                                    <input type="text" class="form-control" id="email" placeholder="Phone"
                                        onchange="myContact(this.value)">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message" class="sr-only">Message</label>
                            <textarea name="message" id="message" cols="30" rows="10" class="form-control"
                                placeholder="Write your message" onchange="myMessage(this.value)"></textarea>
                        </div>
                        <div class="form-group">
                            <a id="contact_support" class="btn btn-primary"
                                href="mailto:telixcel@goldenunion.group?subject= Support Telixcel from Website&body=Name:%0d%0aPhone:%0d%0aMessage:%0d%0a">
                                Send
                            </a>
                        </div>
                    </form>
                </div>
                <div class="col-md" id="map">
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
                    <div class="row rext-right mb-5">
                        <div class="text-justify">
                            <p class="lead">Far far away, behind the word mountains, far from the countries Vokalia and
                                Consonantia, there live the blind texts. Feel free to send us an email to <a
                                    href="mailto:telixcel@goldenunion.group">telixcel@goldenunion.group</a></p>
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
                <div class="col-md-3 text-left ftco-animate">
                    <h5 class="text-uppercase ftco-uppercase">The Best tool for</h5>
                </div>
                <div class="col-md-9 text-center ftco-animate">
                    <div class="row justify-content-center mb-5">
                        <div class="col-md-7">

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

    <h1>
        <a href="" class="typewrite" data-period="2000"
            data-type='[ "Hi, Im Si.", "I am Creative.", "I Love Design.", "I Love to Develop." ]'>
            <span class="wrap"></span>
        </a>
    </h1>

    <footer class="ftco-footer ftco-bg-dark">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md">
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
                        <div class="col-md">
                            <div class="ftco-footer-widget mb-4">
                                <h2 class="ftco-heading-2">Our Contact</h2>
                                <ul class="list-unstyled">
                                    <li><a href="#" class="py-2 d-block">Support</a></li>
                                    <li><a href="#" class="py-2 d-block">Offices</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md">
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
                    <div class="ftco-footer-widget mb-4">
                        <h2 class="ftco-heading-2 ">Follow us</h2>

                        <ul class="ftco-footer-social1 list-unstyled float-md-right1 float-lft"
                            style=" display: flex; text-align: left; ">
                            <li class="pr-4"><a href="#"><span class="icon-twitter"></span></a></li>
                            <li class="pr-4"><a href="#"><span class="icon-facebook"></span></a></li>
                            <li class="pr-4"><a href="#"><span class="icon-instagram"></span></a></li>
                        </ul>
                        <h1 class="ftco-heading-21 text-white navbar-brand"><img
                                src="https://telixcel.s3.ap-southeast-1.amazonaws.com/imgs/logo-white-200.png"
                                title="{{ env('APP_NAME')}}" style="width: 200px;-webkit-filter: brightness(1000%); /* Safari 6.0 - 9.0 */
  filter: brightness(1000%);" /></h1>
                        <p>&copy; {{ env('APP_NAME')}} {{date('Y')}}. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10"
                stroke="#4586ff" />
        </svg></div>


    <script src="frontend/js/all.js"></script>
    <script src="frontend/js/typeit.js"></script>
    <script>
    var app = document.getElementById('aksen');

    var typewriter1 = new TypeIt(app, {
        loop: true,
        strings: [$('.title-text-1').text(), $('.title-text-2').text()],
        speed: 85,
        nextStringDelay: [1000, 5000],
        startDelay: 1000,
        breakLines: false,
        loopDelay: false,
        cursor: true,
    });

    var tool = document.getElementById('best');

    var typewriter2 = new TypeIt(tool, {
        loop: true,
        strings: [$('.tool-1').text(), $('.tool-2').text()],
        speed: 85,
        nextStringDelay: [1000, 5000],
        startDelay: 1000,
        breakLines: false,
        loopDelay: false,
        cursor: true,
    });

    function myName(val) {
        var url = $('#contact_support').attr('href');
        var new_url = url.replace("Name:", "Name: " + val);
        $('#contact_support').attr('href', new_url);

    }

    function myContact(val) {
        var url = $('#contact_support').attr('href');
        var new_url = url.replace("Phone:", "Phone: " + val);
        $('#contact_support').attr('href', new_url);
    }

    function myMessage(val) {
        var url = $('#contact_support').attr('href');
        var new_url = url.replace("Message:", "Message: " + val);
        $('#contact_support').attr('href', new_url);
    }
    </script>
    <script>
    var TxtType = function(el, toRotate, period) {
        this.toRotate = toRotate;
        this.el = el;
        this.loopNum = 0;
        this.period = parseInt(period, 10) || 2000;
        this.txt = '';
        this.tick();
        this.isDeleting = false;
    };

    TxtType.prototype.tick = function() {
        var i = this.loopNum % this.toRotate.length;
        var fullTxt = this.toRotate[i];

        if (this.isDeleting) {
            this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
            this.txt = fullTxt.substring(0, this.txt.length + 1);
        }

        this.el.innerHTML = '<span class="wrap">' + this.txt + '</span>';

        var that = this;
        var delta = 200 - Math.random() * 100;

        if (this.isDeleting) {
            delta /= 2;
        }

        if (!this.isDeleting && this.txt === fullTxt) {
            delta = this.period;
            this.isDeleting = true;
        } else if (this.isDeleting && this.txt === '') {
            this.isDeleting = false;
            this.loopNum++;
            delta = 500;
        }

        setTimeout(function() {
            that.tick();
        }, delta);
    };

    window.onload = function() {
        var elements = document.getElementsByClassName('typewrite');
        for (var i = 0; i < elements.length; i++) {
            var toRotate = elements[i].getAttribute('data-type');
            var period = elements[i].getAttribute('data-period');
            if (toRotate) {
                new TxtType(elements[i], JSON.parse(toRotate), period);
            }
        }
        // INJECT CSS
        var css = document.createElement("style");
        css.type = "text/css";
        css.innerHTML = ".typewrite > .wrap { border-right: 0.08em solid #fff}";
        document.body.appendChild(css);
    };
    </script>
</body>

</html>
