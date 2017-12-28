module Jekyll
  class JekyllVersion < Liquid::Tag
    @@version = `jekyll --version`
    def render(context)
      @@version
    end
  end
end

Liquid::Template.register_tag('version', Jekyll::JekyllVersion)
