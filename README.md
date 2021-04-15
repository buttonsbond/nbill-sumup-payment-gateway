# nbill-sumup-payment-gateway
A payment processor gateway plugin for nBill billing software of NetShine Software Ltd a billing component for Joomla
Uses the SumUp API.

I have been a long time user of the excellent nBill billing software for Joomla. Sadly the component is no longer maintained, Russell, the author sold on to Encke Technologies who have since abandoned the project without warning to the existing user base. This is a great shame. Unfortunately the software was a commercial project though before Russell sold he gave existing clients the opportunity to purchase an unrestricted version (which I did) - the license I think does not permit to share or create a fork of that code.
I downloaded the documentation using waybackarchive website so that I could develop this payment gateway.
I am sure there are still lots of people using nBill, and so I hope they might find this payment gateway useful.

** Pre-installation procedure **

If you download the latest release ZIP file from this github note that it will not install using the nBill extensions installer, you must first of all extract all the files, then go into the resulting folder, select all the files within and compress to a new ZIP file - it is this ZIP file you must use with the extensions installer.
In case you had a failed install you may need to cleanup the folders the extensions installer may have created.

Use nBill's extension installer to install the zip file you created.

** Upgrade procedure **

As pre-installation procedure. It is not necessary to uninstall the older version, nBill will simply upgrade the plugin.

====

You can configurate the gateway from Website/Payment gateways menu from the nBill backend. You're going to need some information from your SumUp account!

Once logged in to your sumup account you will first of all need to go to the 'For Developers' section which you'll find under profile. You will need to create some OAuth credentials for Web App but first fill in the section consent screen (the plugin doesn't actually require this but SumUp do before you can create the OAuth stuff). Create client credentials - give the client a name (something like Your company name SumUp payments), web app, and redirect URL (get the redirect URL from the plugins config screen).
Once you've done this download the file as you will need the client_id and client_secret to configure the plugin. You will also need your Merchant ID which you will find under your SumUp profile - profile details, business information.
Now you'll need to ask SumUp to enable the scope 'payments' for you - I started off with test credentials whilst I developed the plugin. It seems to work.
It will probably take a day or 3 for SumUp to come back to you on this. The plugin won't work until 'payments' is enabled. No idea why it isn't enabled by default!

The config. for the component has help for each field so hopefully you shouldn't find it too onerous.

If you find the plugin useful, please consider buying me a beer, you can donate here: 

https://www.paypal.com/donate?hosted_button_id=EPJLLK8V84GFC

If you haven't signed up for SumUp yet, use my referral link and we both get rewarded (at the time of writing, 15 euros each, so that gives you money off your terminal device, well worth having) - http://r.sumup.com/referrals/quskP
