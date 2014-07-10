
# identify potential conflicting libraries, use them to load widget

scriptPath = window.LRWidgetLoaderPath;

if(!log = @console?.log)
    log = ->

if(jq = @jQuery)
    log('loading via jQuery')
    jq(->
        jq.getScript(scriptPath)
    )

else if(head = @head)
    log('loading via head')
    head(=>
        head.js(scriptPath)
    )

else
    log('loading via script tag')
    loader = document.createElement('script')
    loader.type = 'text/javascript'
    loader.src = loader_path

    s = document.getElementsByTagName('script')[0]
    s.parentNode.insertBefore(loader, s)
