module Jekyll
  module Tagged
    def tagged(site)
      site.posts.flat_map {|post| post['tags'] }.uniq
    end
  end
end

Liquid::Template.register_filter(Jekyll::Tagged)
