<div>
<h1>Signup</h1>
<form method="POST" action="/signup">
  <fieldset>
    <legend class="a11y-hidden">
      WebPageTest Signup Credentials
    </legend>
    <div class="inputs">
      <div>
        <label for="name">Name</label>
        <input type="text" name="name" pattern="<?= $contact_info_pattern ?>" required />
      </div>

      <div>
        <label for="company-name">Company</label>
        <input type="text" name="company-name" />
      </div>

      <div>
        <label for="email">Email</label>
        <input type="email" name="email" required  />
      </div>

      <div>
        <label for="password">Password</label>
        <input type="password" name="password" pattern="<?= $password_pattern ?>" required  />
      </div>

      <div>
        <label for="confirm-password">Confirm Password</label>
        <input type="password" name="confirm-password" pattern="<?= $password_pattern ?>" required  />
      </div>
    </div>

    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>" />
    <button type="submit">Continue</button>
    <p>By signing up, I agree to WebPageTest's <a href="https://www.catchpoint.com/trust#privacy" target="_blank" rel="noopener">Privacy Statement</a> and <a href="/terms.php" target="_blank" rel="noopener">Terms of Service</a>.</p>
  </fieldset>
</form>
</div>
