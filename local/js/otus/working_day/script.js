BX.namespace('Otus.working_day');

BX.addCustomEvent("onTimeManWindowOpen", function(event) {
    
    BX.Otus.working_day.buttonStartDayOld = BX.findChild(event.LAYOUT, {
        'class' : "ui-btn --air ui-btn-md ui-icon-set__scope --with-left-icon --style-filled ui-btn-no-caps"
    }, true);
    
    BX.Otus.working_day.LAYOUT = event.LAYOUT;
    
    if(BX.Otus.working_day.buttonStartDayOld)
    {
        BX.Otus.working_day.createButton(BX.Otus.working_day.buttonStartDayOld);
    }
});

BX.addCustomEvent('onPlannerDataRecieved', function(popup){
    BX.Otus.working_day.buttonReStartDayOld = BX.findChild(BX.Otus.working_day.LAYOUT, {
        'class' : "ui-btn --air ui-btn-md ui-btn-icon-refresh ui-icon-set__scope --with-left-icon --style-filled ui-btn-no-caps"
    }, true);
    
    if(BX.Otus.working_day.buttonReStartDayOld)
    {
        BX.Otus.working_day.createButton(BX.Otus.working_day.buttonReStartDayOld);
    }
});

BX.Otus.working_day.confirmPopup = function(oldButton)
{
    var idPopup = "popup_confirm_working_day";
    
    let confirmText, confirmTitle;
    
    if (oldButton.className.includes('ui-btn-icon-refresh')) {
        confirmText = BX.message("CONTENT_POPUP_RESTART");
        confirmTitle = BX.message("TITLE_BAR_POPUP_RESTART");
    } else {
        confirmText = BX.message("CONTENT_POPUP_CONFIRM");
        confirmTitle = BX.message("TITLE_BAR_POPUP_CONFIRM");
    }
    
    const popupConfirm = BX.PopupWindowManager.create(idPopup,null,{
        content: confirmText,
        titleBar: confirmTitle,
        width: 400,
        height: 200,
        closeByEsc: true,
        draggable: false,
        closeIcon: true,
        overlay: {
            backgroundColor: 'black',
            opacity: 500
        }, 
        buttons:[
            new BX.PopupWindowButton({
                text: BX.message("BTN_TEXT_CONFIRM"),
                className: 'ui-btn --air ui-btn-md --style-filled-success ui-btn-no-caps',
                events: {
                    click: function(){
                        BX.fireEvent(oldButton, 'click');
                        popupConfirm.close();
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: BX.message("BTN_TEXT_CANCEL"),
                className: 'ui-btn --air ui-btn-md --style-plain ui-btn-no-caps',
                events: {
                    click: function() {
                        popupConfirm.close();
                    }
                }
            })
        ],
    });
    
    popupConfirm.show();
}

BX.Otus.working_day.createButton = function(oldButton)
{
    let buttonText, buttonIcon, buttonId;
    
    if (oldButton.className.includes('ui-btn-icon-refresh')) {
        buttonText = BX.message("RESTART_BUTTON_TEXT");
        buttonIcon = BX.UI.Button.Icon.REFRESH;
        buttonId = "btn_restart_working_day";
    } else {
        buttonText = BX.message("START_BUTTON_TEXT");
        buttonIcon = BX.UI.Button.Icon.START;
        buttonId = "btn_start_working_day";
    }
    
    const newButton = new BX.UI.Button({
        id: "my-button-" + buttonId,
        text: buttonText,
        props: {
            id: buttonId
        },
        className: "ui-btn --air ui-btn-md ui-icon-set__scope --with-left-icon ui-btn-no-caps",
        onclick: function(btn, event) {
            BX.Otus.working_day.confirmPopup(oldButton);
        },
        icon: buttonIcon,
    });
    
    if(!BX.isNodeHidden(oldButton))
    {
        BX.hide(oldButton);
        BX.insertBefore(newButton.getContainer(), oldButton);
    }
    
    return newButton;
}