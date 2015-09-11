## CipeMotion Media Library

A WordPress like (but better, duh) media library with file picker component.

### Installation

Publish the config file:

```
php artisan vendor:publish --provider="CipeMotion\Medialibrary\ServiceProvider" --tag="config"
```

Publish the migrations file:

```
php artisan vendor:publish --provider="CipeMotion\Medialibrary\ServiceProvider" --tag="migrations"
```

Run the migrations:

```
php artisan migrate
```

### Configuration

//

### Relations

On your models add:

```
public function attachebles()
{
    return $this->morphToMany(\CipeMotion\Medialibrary\Entities\File::class, 'attacheable');
}
```
