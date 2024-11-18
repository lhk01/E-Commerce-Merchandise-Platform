# <img src="https://github.com/user-attachments/assets/df63b110-0841-4858-8c37-8975d1ae9f44" width="80" /> E-Commerce Merchandise Platform - Merchsystem  



Welcome to **Merchsystem** — an e-commerce platform designed to centralize all gaming products, accessories, and plush items in one convenient place. Merchsystem replicates the full functionality of a real-world e-commerce platform, offering users a seamless shopping experience.  

Whether you're a gamer searching for premium merchandise or a developer curious about implementing a robust e-commerce backend, this project has something for everyone.  

---

## 🛠️ Features 

### User-Centric Features  
- **User Authentication**: Secure login and registration system with reCAPTCHA to prevent bots.  
- **Email Functionality**: Integrated with PHPMailer to send OTPs and transactional emails.  
- **Payment Gateway**: A fully functional payment system to facilitate purchases.  

### Admin & Backend Features  
- **Product Management**: Add, update, and remove products with ease.  
- **Order Management**: Manage user orders and update statuses dynamically.  
- **Database Integration**: Built with MySQL to efficiently manage product data and user records.  

---

## 🚀 Technologies Used  

- **Frontend**: HTML, CSS, JavaScript  
- **Backend**: PHP  
- **Database**: MySQL  
- **Local Server**: XAMPP  
- **Email Service**: PHPMailer  

---

## 📚 Installation Guide  

Follow these steps to set up Merchsystem on your local machine:

### Prerequisites  
- PHP (7.4 or later)  
- Composer  
- XAMPP  

### Steps  

1. **Clone the Repository**  
   ```bash  
   git clone https://github.com/lhk01/E-Commerce-Merchandise-Platform.git  

2. **Install Dependencies**  
   Use Composer to install the required PHP packages, including PHPMailer.  
   ```bash  
   composer require phpmailer/phpmailer  
   ```  

3. **Configure Environment**  
   - Set up your database in XAMPP.  
   - Update the database credentials in the project configuration file (e.g., `config.php`).  

4. **Run the Application**  
   Start XAMPP, ensure Apache and MySQL are running, and open the project in your browser (e.g., `http://localhost/merchsystem`).  

---

## 📧 Email Functionality with PHPMailer  

This project utilizes **PHPMailer** for email functionalities such as sending OTPs and transactional messages.  

### Install PHPMailer  
Run the following Composer command to include PHPMailer in your project:  
```bash  
composer require phpmailer/phpmailer  
```  

### Example Code  
Here’s an example of how to use PHPMailer in Merchsystem:  
```php  
use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;  

require 'vendor/autoload.php';  

$mail = new PHPMailer(true);  

try {  
    $mail->isSMTP();  
    $mail->Host       = 'smtp.example.com';  
    $mail->SMTPAuth   = true;  
    $mail->Username   = 'your-email@example.com';  //your email name
    $mail->Password   = 'your-password';  // your email app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
    $mail->Port       = 587;  

    $mail->setFrom('your-email@example.com', 'Merchsystem');  
    $mail->addAddress('recipient@example.com');  

    $mail->isHTML(true);  
    $mail->Subject = 'Welcome to Merchsystem!';  
    $mail->Body    = 'Your OTP is <b>123456</b>';  

    $mail->send();  
    echo 'Message has been sent';  
} catch (Exception $e) {  
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";  
}  
```  



---

## 📄 License  

This project is licensed under the [MIT License](LICENSE).  

---

## 👥 Contributors  

We are a team of passionate developers who collaborated on this Capstone Project:  

| Name             | GitHub Profile                       |
|------------------|--------------------------------------|
| **Leong Hoong Kai** | [@lhk01](https://github.com/lhk01) |
| **Chew Zhi Boon**      | [@Rey-3](https://github.com/Rey-3) |
| **Goay Wei Jun**    | [@Blackmanchoco](https://github.com/Blackmanchoco) |

---

## 🌟 Acknowledgments  

Special thanks to the developers and contributors of open-source libraries, especially PHPMailer, for making this project possible!  

