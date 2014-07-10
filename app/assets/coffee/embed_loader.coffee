
# identify potential conflicting libraries, use them to load widget

scriptPath = window.LRWidgetLoaderPath;

if(!l = @console?.log)
    l = ->

if(jq = @jQuery)
    l('loading via jQuery')
    jq(->
        jq.getScript(scriptPath)
    )

else if(head = @head)
    l('loading via head')
    head(=>
        head.js(scriptPath)
    )

else
    l('loading via script tag')
    loader = document.createElement('script')
    loader.type = 'text/javascript'
    loader.src = loader_path

    s = document.getElementsByTagName('script')[0]
    s.parentNode.insertBefore(loader, s)
