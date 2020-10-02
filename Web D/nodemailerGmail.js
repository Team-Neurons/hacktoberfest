const nodemailer = require('nodemailer');
require('dotenv').config();

function configMail(content,emailId,response){
nodemailer.createTestAccount((err, account) => {
    // create reusable transporter object using the default SMTP transport
    let transporter = nodemailer.createTransport({
        service: 'gmail',
        auth: {
        user: process.env.EMAIL,
        pass: process.env.PASSWORD
    }
    });

    // https://myaccount.google.com/lesssecureapps?pli=1        (On this setting, from your account)

    // setup email data with unicode symbols
    let mailOptions = {
        from: '', // sender address
        to: emailId, // list of receivers
        subject: '[Confidential] New update from wizard', // Subject line
        text:"Dear User, " + content  + "Thank you !", // plain text body
        //html: `<b>${content} </b> ` // html body
    };

    // send mail with defined transport object
    transporter.sendMail(mailOptions, (error, info) => {
        if (error) {
            console.log("Mail NOT Send ERROR.....",error);
            //response.send("Can't Send Mail , Some Error");
            //return console.log(error);
        }
        console.log("Mail Send SuccessFully.....");
       //response.send("Mail Send SuccessFully.....");
    });
});
}
module.exports = configMail;
