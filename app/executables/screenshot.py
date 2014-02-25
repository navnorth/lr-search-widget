#!/usr/bin/env python
#-*- coding:utf-8 -*-

from PyQt4.QtCore import *
from PyQt4.QtGui import *
from PyQt4.QtWebKit import *

class browser(QWebView):
    def __init__(self, url, targetFile, parent=None):
        super(browser, self).__init__(parent)

        self.targetFile = targetFile

        self.settings().setAttribute(QWebSettings.PluginsEnabled, True)

        self.timerScreen = QTimer()
        self.timerScreen.setInterval(2000)
        self.timerScreen.setSingleShot(True)
        self.timerScreen.timeout.connect(self.takeScreenshot)

        self.loadFinished.connect(self.timerScreen.start)
        self.load(QUrl(url))



    def takeScreenshot(self):
        frame = self.page().mainFrame()

        self.page().setViewportSize(QSize(1024, 768))

        image   = QImage(self.page().viewportSize(), QImage.Format_ARGB32)
        painter = QPainter(image)

        frame.render(painter)

        painter.end()
        image.save(self.targetFile)

        sys.exit()

if __name__ == "__main__":
    import  sys
    app  = QApplication(sys.argv)
    (url, targetFile) = sys.argv[1:3]
    main = browser(url, targetFile)
    app.exec_()
