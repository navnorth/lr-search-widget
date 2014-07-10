define(['jquery'], (jq) ->
    local = jq.noConflict()

    local.log = ->
        if(arguments.length == 1)
            console.log(arguments[0]) if console.log
            return arguments[0]
        else
            console.log(arguments) if console.log
            return arguments


    return local
)

