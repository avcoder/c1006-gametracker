# TIPS WHEN INTEGRATING BACK

- cut/paste from h1 tag onwards
- pay attention if your original game.php had different variable names, patterns, required, action= etc.

## save-upload.php

- lines 6-9 goes where?

  ```php
  $name = $_FILES['pic']['name'];
  $tmp_name = $_FILES['pic']['tmp_name'];
  $type = mime_content_type($tmp_name);
  $size = $_FILES['pic']['size'];
  ```

  - How will you get that data into validate_game fn?

- lines 13-19 goes where?

  ```php
  if ($size > 1000000) {
      $errors['pic'] = "Image must be less than 1MB";
  }

  if (!($type == 'image/jpeg' || $type == 'image/png')) {
      $errors['pic'] = "Image format must be .jpg or .png";
  }
  ```

- lines 21-23 go where?
  ```php
  if (empty($errors)) {
    move_uploaded_file($tmp_name, "uploads/" . session_id() . $name);
  }
  ```
  - What if the user didnt upload pic; will you still try to move_uploaded_file?
- Echo error msg? What if there's no error message?
  ```html
  <p class="px-3 pb-2 text-danger">Error message goes here</p>
  ```
- How about actually inserting the image unique filename in db?

  - How will you modify your insert statement?

- if you get an error page, comment out header and use echo $e

```php
        } catch (Exception $e) {
            // header("Location: error.php");
            echo $e;
            exit;
        }
```

## How to Program in 2 steps

1. WHAT DO YOU WANT TO DO?
1. HOW ARE YOU GOING TO DO IT?
