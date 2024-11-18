    <?php

    session_start();
    include("../header-footer/header.php");

    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact</title>
        <link rel="stylesheet" href="../css/contact-us.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">    
    
    </head>
    <body>

            <div class="contact-container">

                <div class="contact-form-section">

                    <h2><i class="fa fa-paper-plane"></i> Send Us a Message</h2>

                    <form class="contact-form" id="contact-form" action="../function/contact-us.php" method="post">

                        <div class="form-group">

                            <label for="name"><i class="fa fa-user"></i> Name:</label>

                            <input type="text" id="name" name="name" required placeholder="Your name">

                        </div>

                        <div class="form-group">

                            <label for="email"><i class="fa fa-envelope"></i> Email:</label>

                            <input type="email" id="email" name="email" required placeholder="your.email@example.com">
                    
                        </div>

                        <div class="form-group">
                        
                            <label for="subject"><i class="fa fa-tag"></i> Subject:</label>

                            <input type="text" name="subject" id="subject" required placeholder="feedback">

                        </div>

                        <div class="form-group">

                            <label for="message"><i class="fa fa-comment"></i> Message:</label>

                            <textarea id="message" name="message" required placeholder="Your message here..."></textarea>
                        
                        </div>
                        <div class = "message">
                            <?php
                                if(isset($_SESSION['msg']) && !empty($_SESSION['msg'])){
                                    echo "<p>".$_SESSION['msg']."</p>";
                                    unset($_SESSION['msg']);
                                }
                            ?>
                        </div>
                        <button type="submit" class="submit-button pulse">

                            <i class="fa fa-paper-plane"></i> Send Message

                        </button>

                    </form>

                </div>

                <div class="contact-info-section">

                    <h2><i class="fa fa-info-circle"></i> Contact Information</h2>

                    <div class="contact-info-grid">

                        <div class="contact-info-item">

                            <i class="fa fa-map-marker contact-icon"></i>

                            <div class="contact-text">

                                <h3>Location</h3>

                                <p>1-Z Lebuh Bukit Jambul 11900 Penang, Malaysia</p>

                                <a href="https://maps.google.com" target="_blank" class="directions-link">Get Directions</a>
                            
                            </div>

                        </div>
                        
                        <div class="contact-info-item">

                            <i class="fa fa-phone contact-icon"></i>

                            <div class="contact-text">

                                <h3>Phone</h3>

                                <p>+6012-3456789</p>

                                <span class="availability">Available 7 days a week</span>

                            </div>

                        </div>

                        <div class="contact-info-item">

                            <i class="fa fa-envelope contact-icon"></i>

                            <div class="contact-text">
                            
                                <h3>Email</h3>

                                    <p>merchsystem@gmail.com</p>

                                <span class="response-time">We respond within 24 hours</span>

                            </div>

                        </div>

                        <div class="contact-info-item">

                            <i class="fa fa-clock-o" aria-hidden="true"></i>

                            <div class="contact-text">

                                <h3>Hours</h3>

                                <p>Mon-Fri: 9am-9pm</p>

                                <p>Sat-Sun: 10am-10pm</p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="map-section">

                <h2><i class="fa fa-map-marked-alt"></i> Find Us</h2>

                <div class="map-wrapper">

                    <div class="map-container">

                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3884.5591895007187!2d100.27929577485682!3d5.341609135768617!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x304ac048a161f277%3A0x881c46d428b3162c!2sINTI%20International%20College%20Penang!5e1!3m2!1sen!2smy!4v1730451288673!5m2!1sen!2smy"
                            width="100%" 
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>

                    </div>
                    <div class="map-overlay">

                        <div class="business-hours">

                            <h3>Business Hours</h3>

                            <ul>

                                <li><span>Monday - Friday:</span> 9:00 AM - 9:00 PM</li>

                                <li><span>Saturday - Sunday:</span> 10:00 AM - 10:00 PM</li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>


   
    </body>
    </html>
    <?php include("../header-footer/footer.php");?>
