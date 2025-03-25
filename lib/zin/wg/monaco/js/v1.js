setTimeout(function()
{
    createMonaco(id, action, options, diffContent, onMouseDown, onMouseMove, vsPath, clientLang);
    resize(id);
}, 200);

function createMonaco(id, action, options, diffContent, onMouseDown, onMouseMove, vsPath, clientLang)
{
    if(!options.minimap) options.minimap = {enabled: false};
    require.config({
        paths: {vs: vsPath},
        'vs/nls': {
            availableLanguages: {
                '*': clientLang
            }
        }
    });

    let decorations = [];
    let programmaticSelectionRange = null;

    require(['vs/editor/editor.main'], function ()
    {
        if(action == 'diff')
        {
            options.renderSideBySide = $.cookie.get('renderSideBySide') == 'true';

            options.lineNumbers = function(number)
            {
                const newlc = diffContent.line.new;
                return newlc[number - 1];
            };

            modifiedEditor = monaco.editor.createDiffEditor(document.getElementById(id), options);
            window.modifiedEditor = modifiedEditor;

            modifiedEditor.setModel({
                original: monaco.editor.createModel(diffContent.code.old, options.lang),
                modified: monaco.editor.createModel(diffContent.code.new, options.lang),
            });

            editor = modifiedEditor.getModifiedEditor();

            const getOriginalEditor = modifiedEditor.getOriginalEditor();
            getOriginalEditor.updateOptions({
                lineNumbers: function(number)
                {
                    var oldlc = diffContent.line.old;
                    return oldlc[number - 1];
                }
            });

            if(onMouseDown) getOriginalEditor.onMouseDown(function(obj){eval(onMouseDown + '(obj)')})
            if(onMouseMove) getOriginalEditor.onMouseMove(function(obj){eval(onMouseMove + '(obj)')})
        }
        else
        {
            editor = monaco.editor.create(document.getElementById(id), options);
        }
        if(onMouseDown) editor.onMouseDown(function(obj){eval(onMouseDown + '(obj)')})
        if(onMouseMove) editor.onMouseMove(function(obj){eval(onMouseMove + '(obj)')})

        if(selectedLines)
        {
            const lines = selectedLines.split(',');
            const startLine = parseInt(lines[0]);
            let   endLine   = parseInt(lines[1] || startLine + 1);
            let   startCol  = 1;
            let   endCol    = 0;
            if(lines.length == 4)
            {
                startCol = parseInt(lines[2]);
                endCol   = parseInt(lines[3]);
            }
            if(endCol == 0) endLine += 1;

            const range = new monaco.Range(startLine, startCol, endLine, endCol);
            programmaticSelectionRange = range;
            editor.setSelection(range);
            updateDecorations();
        }

        function updateDecorations()
        {
            editor.deltaDecorations(decorations, []);
            decorations = [];

            if(programmaticSelectionRange)
            {
                decorations.push({
                    range: programmaticSelectionRange,
                    options: {
                        className: selectedClass,
                        isWholeLine: false
                    }
                });
            }

            decorations = editor.deltaDecorations([], decorations);
        }
    });
}

function resize(id)
{
    var $ = window.$ == undefined ? parent.$ : window.$;
    var windowHeight   = $(window).height();
    var headerHeight   = parseInt($('#header').height());
    var mainNavbar     = parseInt($('#mainNavbar').height());
    var mainMenuHeight = parseInt($('#mainMenu').css('padding-top')) + parseInt($('#mainMenu').css('padding-bottom'));
    var appTabsHeight  = parseInt($('#appTabs').height());
    var appsBarHeight  = parseInt($('#appsBar').height());
    var tabsHeight     = parseInt($('#fileTabs .tabs-navbar').height());

    headerHeight   = headerHeight ? headerHeight : 0;
    appsBarHeight  = appsBarHeight ? appsBarHeight : 0;
    tabsHeight     = tabsHeight ? tabsHeight : 0;
    appTabsHeight  = appTabsHeight ? appTabsHeight : 0;
    mainMenuHeight = mainMenuHeight ? mainMenuHeight : 0;
    mainNavbar     = mainNavbar ? mainNavbar : 0;

    var codeHeight     = windowHeight - headerHeight - appsBarHeight - tabsHeight - appTabsHeight - mainMenuHeight - mainNavbar;

    if(codeHeight > 0) $.cookie.set(id + 'Height', codeHeight);

    $('#' + id).css('height', $.cookie.get(id + 'Height'));
}
