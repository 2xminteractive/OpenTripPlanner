// this allows us to display the OTP UI with a predefined apiResponse (XML)
otp.planner.Forms.prototype.submitSuccessXML = function(response)
{
	var responseXML;
	if (window.DOMParser)
	{
        var parser=new DOMParser();
        responseXML=parser.parseFromString(response,"text/xml");
	}
	else // Internet Explorer
	{
        responseXML=new ActiveXObject("Microsoft.XMLDOM");
        responseXML.async=false;
        responseXML.loadXML(response);
	}
    var result = this.planner.newTripPlan(responseXML, this.getFormData());
    if (!result)
    {
        this.tripRequestError(responseXML);
        return;
    }
    if(this.poi) this.poi.clearTrip();
    //otp.util.Analytics.gaEvent(otp.util.Analytics.OTP_TRIP_SUCCESS);
};

// removed the part about URL & title changes; we don't need/want that
otp.planner.Planner.prototype.tabChange = function(tabPanel, activeTab) 
{
    this.m_renderer.clear();
    var newTab = this.m_tabs[activeTab.id];
    
    // remove the topo graph from the south panel, if applicable 
    var oldTab = this.m_tabs[this.m_activeTabID];
    if(oldTab != null && oldTab.topoRenderer != null) {
        oldTab.topoRenderer.removeFromPanel();
    }

    // draw the new tab, if applicable
    if (newTab != null) {
        this.m_activeTabID = activeTab.id;
        newTab.draw();
    } else {
        this.m_activeTabID = 0;
        this.controller.deactivate(this.CLASS_NAME);
        this.m_forms.panelActivated();
    }
    
    // hide the south panel, if empty
    if (this.ui.innerSouth.isVisible()  && this.ui.innerSouth.getEl().dom.childNodes.length == 0) {
        this.ui.innerSouth.hide();
        this.ui.viewport.doLayout();
    }

	/*
	// update the dynamic link to the current trip plan
    // TODO: is the 'plan a trip' tab always tab 0?
    if (this.m_activeTabID === 0) {
        location.hash = '#/';
        document.title = 'OpenTripPlanner Map';
    }
    else {
        // we're on a TP tab
        // template for the dynamic url
        if (this.m_hashTemplate == null) {
            this.m_hashTemplate = new Ext.XTemplate('#/' + otp.planner.ParamTemplate).compile();
        }
        location.hash = this.m_hashTemplate.apply(newTab.request);
        // update the title so folks bookmark something meaningful
        document.title = newTab.getTitle() + ' - OpenTripPlanner Map';
    }
    */
}

otp.planner.TripTab.prototype.getPanel = function()
{
    // Tab options
    if (isFrench()) {
        this.m_panel.title = 'Trajets Possibles';
    } else {
        this.m_panel.title = 'Available Options';
    }
    this.m_panel.tabTip = '';
    this.m_panel.closable = false;
    return this.m_panel;
}

// Some customization for date/time
otp.util.DateUtils.DATE_TIME_FORMAT_STRING = "D, M j H:i";
otp.util.DateUtils.TIME_FORMAT_STRING = "H:i";

function isFrench() {
    // TODO Auto-select lang based on browser HTTP requests; this below is not right...
    var language = window.navigator.userLanguage || window.navigator.language;
    return language.indexOf('fr') > -1;
}

Ext.onReady(function()
{
    // Use $, not Euros, for fares.
    otp.locale.French.labels.fare_symbol = '$';

    if (isFrench()) {
        otp.config.locale = otp.locale.French;
    } else {
        otp.config.locale = otp.locale.English;
    }

    var c = new otp.application.Controller(otp.config);
    c.planner.m_forms.submitSuccessXML(apiResponse);
    
    // Tabs width
    c.planner.m_tabPanel.tabWidth = 140;
    c.planner.m_tabPanel.autoSizeTabs();
    
    // Hide the OTP logo
    $('west-panel').style.top = 0;
});

otp.planner.Forms.prototype.getFormData = function(url)
{
	return {};
};
