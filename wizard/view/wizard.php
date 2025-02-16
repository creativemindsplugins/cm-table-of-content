<style>div.error{display:none;}</style>
<div class="cm-wizard-step step-0">
    <h1>Welcome to the Table of Contents Setup Wizard</h1>
    <p>Thank you for installing the CM Table of Contents plugin!</p>
    <p>This plugin enhances your website by automatically generating a table of contents for your posts or pages,<br>making it easier for visitors to navigate through your content and find the information they need.</p>
    <img class="img" src="<?php echo CMTOCF_SetupWizard::$wizard_url . 'assets/img/wizard_logo.png';?>" />
    <p>To help you get started, we’ve prepared a quick setup wizard to guide you through these steps:</p>
    <ul>
        <li>• Configuring the TOC appearance</li>
        <li>• Customizing the TOC style</li>
        <li>• Setting up your first table of contents</li>
    </ul>
    <button class="next-step" data-step="0">Start</button>
    <p><a href="<?php echo admin_url( 'admin.php?page='. CMTOCF_SetupWizard::$setting_page_slug ); ?>" >Skip the setup wizard</a></p>
</div>
<?php echo CMTOCF_SetupWizard::renderSteps(); ?>