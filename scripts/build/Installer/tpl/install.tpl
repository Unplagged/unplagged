<div class="well">
<div class="tabbable">

    <div class="tab-content">
        <div class="tab-pane active" id="step1">
            <form class="form-horizontal" novalidate>
                <legend>Page Information</legend>
                <div class="control-group">
                    <label class="control-label" for="defaultName">Page name<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="defaultName" type="text" id="defaultName" placeholder="Unplagged" required />
                        <span class="help-inline">Will be displayed on top of every page and used within emails.</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="defaultSender">E-Mail sender name<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="defaultSender" type="text" id="defaultSender" placeholder="Unplagged Group" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="defaultEmail">E-Mail sender address<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="defaultEmail" type="email" id="defaultEmail" placeholder="no-reply@example.com" required />
                        <p class="help-block"></p>
                    </div>
                </div>

                <legend>Imprint</legend>
                <div class="control-group">
                    <label class="control-label" for="imprintAddress">Address</label>
                    <div class="controls">
                        <textarea class="input-xlarge" rows="4" name="imprintAddress" type="text" id="imprintAddress" placeholder="Berlin"></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="imprintPhone">Telephone</label>
                    <div class="controls">
                        <input class="input-xlarge" name="imprintPhone" type="text" id="imprintPhone" placeholder="+49 30 1234 567 89" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="imprintEmail">E-Mail</label>
                    <div class="controls">
                        <input class="input-xlarge" name="imprintEmail" type="email" id="imprintEmail" placeholder="contact@example.com" />
                    </div>
                </div>
                <div class="control-group pagination-right">
                    <button type="submit" class="btn btn-primary" data-step-id="2">Proceed</button>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="step2">
            <form class="form-horizontal" novalidate>
                <legend>Database</legend>
                <div class="control-group">
                    <label class="control-label" for="dbHost">Host<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="dbHost" type="text" id="dbHost" placeholder="127.0.0.1" value="127.0.0.1" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="dbUser">User<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="dbUser" type="text" id="dbUser" placeholder="unplagged" value="unplagged" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="dbPassword">Password</label>
                    <div class="controls">
                        <input class="input-xlarge" name="dbPassword" type="password" id="dbPassword" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="dbName">Databasename<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="dbName" type="text" id="dbName" placeholder="unplagged" value="unplagged" required />
                    </div>
                </div>

                <legend>Admin user</legend>
                <div class="control-group">
                    <label class="control-label" for="adminEmail">E-Mail<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="adminEmail" type="email" id="adminEmail" placeholder="admin@example.com" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="adminUsername">Username<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="adminUsername" type="text" id="adminUsername" placeholder="admin" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="adminPassword">Password<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="adminPassword" type="password" id="adminPassword" minlength="6" required />
                    </div>
                </div>

                <div class="control-group pagination-right">
                    <button type="submit" class="btn btn-primary" data-step-id="3">Proceed</button>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="step3">
            <form class="form-horizontal" novalidate>
                <legend>Script paths</legend>
                <p><em>To use the whole feature set of Unplagged, it is recommended to install the command 
                        line tools listed below on your server. If they are not registered with your PATH 
                        enironment variable, please provide the full server path here.</em></p>
                <div class="control-group">
                    <label class="control-label" for="inputTesseract">Tesseract</label>
                    <div class="controls">
                        <input class="input-xlarge" name="tesseractPath" type="text" id="inputTesseract" placeholder="tesseract" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputGhostscript">Ghostscript</label>
                    <div class="controls">
                        <input class="input-xlarge" name="ghostscriptPath" type="text" id="inputGhostscript" placeholder="gs" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputImagemagick">Imagemagick</label>
                    <div class="controls">
                        <input class="input-xlarge" name="imagemagickPath" type="text" id="inputImagemagick" placeholder="convert" />
                    </div>
                </div>
                <div class="control-group pagination-right">
                    <button type="submit" class="btn btn-success" data-step-id="4">Proceed</button>
                </div>
            </form>
        </div>
        
        <div class="tab-pane" id="step4">
            <p><img src="/images/throbber.gif" id="loader" /></p>
            <div id="checks-console"></div>
            <div class="control-group pagination-right">
              <button type="submit" class="btn btn-success" data-step-id="5">Install Unplagged</button>
            </div>
        </div>
        <div class="tab-pane" id="step5">
            <p><img src="/images/throbber.gif" class="loader" /></p>
            <div id="installation-console"></div>
            <div id="config-file"></div>
        </div>
    </div>
</div>
