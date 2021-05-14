## Request Logger
Made by Adam Langley ( https://twitter.com/adamtlangley )

### What is it?
Request logger is a free and open source utility for logging HTTP and DNS requests.

### What can I use it for?
Request logger is perfect for blind or stored attacks such as XSS and SSRF. Because it also logs the DNS requests you won't miss out on requests where HTTP may be stopped by a firewall.

You could could also use it to ExFil data over DNS requests.

A new and unique subdomain can be created for each target/attack or whatever you want.

### Install Instructions

You'll need a domain name with permission to make changes to its DNS records and also a machine with a public IP address ( not got one? get a VM here with $100 free credit https://m.do.co/c/be75f966a8a9 )

For these instructions lets pretend you have a VM with the IP address 123.123.123.123 and a domain name of hacker.com

First create an A record such as vm.hacker.com pointing to 123.123.123.123

Then create an NS record such as log.hacker.com pointing to vm.hacker.com

On your machine install docker and run the below commands making sure you substitute YOUR_IP and YOUR_SUBDOMAIN with the ones you created above.

`docker build -h request-logger .`

`docker run -d -p YOUR_IP:53/udp -p 80:80 request-logger /startup.sh YOUR_IP YOUR_SUBDOMAIN`

Now visit http://YOUR_IP and you should get the request logger website. Click the "Create Session" which will create you a unique subdomain for your domain. 

This subdomain will now start logging and HTTP or DNS requests made to it.