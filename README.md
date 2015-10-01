## CipeMotion Media Library

A WordPress like (but better, duh) media library with file picker component.

### Configuration

Publish the config file:

```
php artisan vendor:publish --provider="CipeMotion\Medialibrary\ServiceProvider" --tag="config"
```

Read through the config file and change what is needed.

### Database

Publish the migrations file:

```
php artisan vendor:publish --provider="CipeMotion\Medialibrary\ServiceProvider" --tag="migrations"
```

Run the migrations:

```
php artisan migrate
```

### Relations

On your owner model add:

```
public function files()
{
    return $this->hasMany(\CipeMotion\Medialibrary\Entities\File::class, 'owner_id');
}
```

On your models add:

```
public function attachebles()
{
    return $this->morphToMany(\CipeMotion\Medialibrary\Entities\File::class, 'attacheable');
}
```