<div class="tabbable" style="margin-bottom: 18px;">
    <ul class="nav nav-pills" id="navigation">
        <li class="active">
            <a id="tab-btn-1" data-tab-id="1" href="#">General</a>
        </li>
        <li class="disabled">
            <a id="tab-btn-2" data-tab-id="2" href="#">Connection</a>
        </li>
        <li class="disabled">
            <a id="tab-btn-3" data-tab-id="3" href="#">Script paths</a>
        </li>
                <li class="disabled">
            <a id="tab-btn-4" data-tab-id="4" href="#">Finish</a>
        </li>
    </ul>

    <div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
        <div class="tab-pane active" id="step1">
            <form class="form-horizontal" novalidate>
                <legend>Portal information</legend>
                <div class="control-group">
                    <label class="control-label" for="defaultName">Portal name<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="defaultName" type="text" id="defaultName" placeholder="Unplagged" required />
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
                        <input class="input-xlarge" name="defaultEmail" type="email" id="defaultEmail" placeholder="no-reply@unplagged.com" required />
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
                        <input class="input-xlarge" name="imprintEmail" type="email" id="imprintEmail" placeholder="contact@unplagged.com" />
                    </div>
                </div>
                <div class="control-group pagination-right">
                    <button type="submit" class="btn btn-primary" data-step-id="2">next step</button>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="step2">
            <form class="form-horizontal" novalidate>
                <legend>Database</legend>
                <div class="control-group">
                    <label class="control-label" for="dbHost">Host<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="dbHost" type="text" id="dbHost" placeholder="127.0.0.1" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="dbUser">User<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="dbUser" type="text" id="dbUser" placeholder="root" required />
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
                        <input class="input-xlarge" name="dbName" type="text" id="dbName" placeholder="unplagged" required />
                    </div>
                </div>

                <legend>Admin user</legend>
                <div class="control-group">
                    <label class="control-label" for="adminEmail">E-Mail<span class="text-error">*</span></label>
                    <div class="controls">
                        <input class="input-xlarge" name="adminEmail" type="email" id="adminEmail" placeholder="admin@unplagged.com" required />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="adminUsername">Shown username<span class="text-error">*</span></label>
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
                    <button type="submit" class="btn btn-primary" data-step-id="3">next step</button>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="step3">
            <form class="form-horizontal" novalidate>
                <legend>Script paths</legend>
                <p><em>To use the whole feature set of Unplagged, it is recommend to install the command 
                        line tools stated below, beforehand and provide their locations here.</em></p>
                <div class="control-group">
                    <label class="control-label" for="inputTesseract">Tesseract</label>
                    <div class="controls">
                        <input class="input-xlarge" name="tesseractPath" type="text" id="inputTesseract" placeholder="tesseract" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputGhostscript">Ghotscript</label>
                    <div class="controls">
                        <input class="input-xlarge" name="ghostscriptPath" type="text" id="inputGhostscript" placeholder="gswin32c" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputImagemagick">Imagemagick</label>
                    <div class="controls">
                        <input class="input-xlarge" name="imagemagickPath" type="text" id="inputImagemagick" placeholder="convert" />
                    </div>
                </div>
                <div class="control-group pagination-right">
                    <button type="submit" class="btn btn-success" data-step-id="4">Install Unplagged</button>
                </div>
            </form>
        </div>
        
        <div class="tab-pane" id="step4">
            <p><img src="/images/throbber.gif" id="installation-loading" /></p>
            <div id="installation-steps"></div>
        </div>
    </div>
</div>
