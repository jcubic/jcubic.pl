require 'liquid'
require 'erb'

# Percent encoding for URI conforming to RFC 3986.
# Ref: http://tools.ietf.org/html/rfc3986#page-12
module URLEncoding
  def url_encode(url)
    return ERB::Util.url_encode(url)
  end
end

Liquid::Template.register_filter(URLEncoding)
