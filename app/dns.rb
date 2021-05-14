require 'socket'
require 'net/http'

if ARGV.length < 1
        puts "Missing Argument, please pass IP address to reply as!"
        exit
end
puts "STARTING DNS LOGGER"
server = UDPSocket.new
server.bind('',53)
        loop {
                d1, d2 = server.recvfrom(1024)
                Thread.new(d1,d2) do |text, sender|
                hex = text.unpack('H*').to_s
                domain_sp = hex[28,hex.length]
                domain = ''
                domain_org = '';
                i2 = 0;
                amt = Integer(hex[26,2],16)
                domain_sp.scan(/.{2}/).map{|i|
                        if i == '00' then
                        break
                        end
                        domain_org += i
                        if i2 == amt then
                                amt = Integer(i,16)
                                domain += '2E'
                                i2 = 0
                        else
                        domain += i
                        i2 += 1
                        end
                }
                start_p = 32 + domain.length
                type = hex[ start_p  ,2]
                dst_ip = ARGV[0]
                domain = domain.gsub(/../) { |pair| pair.hex.chr }
                        if type == '01' then
                        ip_sp = dst_ip.split('.')
                        ack = hex[2,4] + '85000001000100000000' + hex[26,2] + domain_org + '0000010001c00c00010001000006340004' + ip_sp[0].to_i.to_s(16).rjust(2,'0') + ip_sp[1].to_i.to_s(16).rjust(2,'0')  + ip_sp[2].to_i.to_s(16).rjust(2,'0') + ip_sp[3].to_i.to_s(16).rjust(2,'0')
                        server.send ack.scan(/../).map { |x| x.hex.chr }.join  , 0, sender[3], sender[1]
                        else
                        ack = hex[2,4] + '85000001000100000000' + hex[26,2] + domain_org + '0000'+type+'0001c00c00010001000000000000'
                        server.send ack.scan(/../).map { |x| x.hex.chr }.join  , 0, sender[3], sender[1]
                        end
                 case type
                 when "01"
                        dns_type = 'A'
                 when "1c"
                        dns_type = 'AAAA'
                 when "05"
                        dns_type = 'CNAME'
                 when "0f"
                        dns_type = 'MX'
                 when "10"
                        dns_type = 'TXT'
                 else
                        dns_type = 'OTHER'
                 end
                 Net::HTTP.get_response(URI('http://127.0.0.1/api/dns-request?domain=' + domain + '&type=' + dns_type + '&ip=' + sender[3])  )
                 end
        }
