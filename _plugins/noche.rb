require 'zlib'


module Jekyll
  module NoCache
    def nocache(str)
      Zlib::crc32(File.read(str)).to_s(16)
    end
  end
end

Liquid::Template.register_filter(Jekyll::NoCache)
