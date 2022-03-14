# FILE UPLOAD

## SET UP
```console
make init
```
## Usage

```console
curl --location --request POST '127.0.0.1:8080/upload' \
--form '<key>=@"<file_location>"' \
--form '<key>=@"<file_location>"'
```
## Improvements

- If we add multiple archiving methods: new Flysystem adapters needs to be added and App\Filesystem\FilesystemFileFactory modified
- Face a significant increase in request count: horizontal scaling should help. Load balancer should be added and more applications/servers created
- Allow 1GB max file size: the php.ini file should be modified. Since the application does not read the entire file into memory, it should work even with 1GB files
