#!/usr/bin/env python
import os, sys, re, sqlite3
from bs4 import BeautifulSoup

def get_data(html):
    """return dictionary with title url and content of the blog post"""
    tree = BeautifulSoup(html, 'html5lib')

    body = tree.body
    if body is None:
        return None

    for tag in body.select('script'):
        tag.decompose()
    for tag in body.select('style'):
        tag.decompose()
    for tag in body.select('figure'):
        tag.decompose()

    text = tree.find_all("div", {"class": "content"})
    if len(text) > 0:
      text = text[0].get_text(separator='\n')
    else:
      text = None
    title = tree.find_all("h1", {"itemprop" : "title"})
    url = tree.find_all("link", {"rel": "canonical"})
    if len(title) > 0:
      title = title[0].get_text()
    else:
      title = None
    if len(url) > 0:
      url = url[0]['href']
    else:
      url = None
    result = {
      "title": title,
      "url": url,
      "text": text
    }
    return result


if __name__ == '__main__':
  if len(sys.argv) == 2:
    db_file = 'index.db'
    if os.path.exists(db_file):
      os.remove(db_file)
    conn = sqlite3.connect(db_file)
    c = conn.cursor()
    c.execute('CREATE TABLE page(title text, url text, content text)')
    for root, dirs, files in os.walk(sys.argv[1]):
      for name in files:
        if name.endswith(".html") and re.search(r"[/\\]20[0-9]{2}", root):
          fname = os.path.join(root, name)
          f = open(fname, "r")
          data = get_data(f.read())
          f.close()
          if data is not None:
            c.execute('INSERT INTO page VALUES(?, ?, ?)', (data['title'], data['url'], data['text']))
            print("indexed %s" % data['url'])
            sys.stdout.flush()
    conn.commit()
    conn.close()
