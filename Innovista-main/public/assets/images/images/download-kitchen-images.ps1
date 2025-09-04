# Kitchen Product Images Download Script

$images = @(
    @{url="https://tse3.mm.bing.net/th/id/OIP.0q8uf5bT0Bn3Jxe-zdyeEgAAAA?pid=Api&P=0&h=220"; file="kitchen-cabinet-1.jpg"},
    @{url="https://tse1.mm.bing.net/th/id/OIP.u0ywxADa0x2YnBZM9FkRnQHaIL?pid=Api&P=0&h=220"; file="kitchen-cabinet-2.jpg"},
    @{url="https://tse4.mm.bing.net/th/id/OIP.n5LMNvCFUICU8T3dUVnBygHaD7?pid=Api&P=0&h=220"; file="kitchen-cabinet-3.jpg"},
    @{url="https://tse2.mm.bing.net/th/id/OIP.7mfpSlWVtKD6oSeOPSdZxgHaHa?pid=Api&P=0&h=220"; file="kitchen-appliance-1.jpg"},
    @{url="https://tse2.mm.bing.net/th/id/OIP.I9_NSduLwAH0PPKKP8-afQHaLN?pid=Api&P=0&h=220"; file="kitchen-appliance-2.jpg"},
    @{url="https://tse2.mm.bing.net/th/id/OIP.RMYDCRIirxdJPkHxBsGjTwHaEr?pid=Api&P=0&h=220"; file="kitchen-appliance-3.jpg"},
    @{url="https://tse4.mm.bing.net/th/id/OIP.ughzt2Cy630G5htyh7uC2gHaJ4?pid=Api&P=0&h=220"; file="bathroom-marble-1.jpg"},
    @{url="https://tse4.mm.bing.net/th/id/OIP.N1e7vFx8sb2dhD8IPYkljgHaJD?pid=Api&P=0&h=220"; file="bathroom-marble-2.jpg"},
    @{url="https://tse3.mm.bing.net/th/id/OIP.Usnxt1y_d6RZJjS9pGR8iAHaHa?pid=Api&P=0&h=220"; file="bathroom-marble-3.jpg"},
    @{url="https://tse3.mm.bing.net/th/id/OIP.hKKCKvii2N3xGZrEkgxv6QHaHa?pid=Api&P=0&h=220"; file="bathroom-sink-1.jpg"},
    @{url="https://tse4.mm.bing.net/th/id/OIP.Xu7i0baWvjQfMMV5oUkg_QHaE8?pid=Api&P=0&h=220"; file="bathroom-sink-2.jpg"},
    @{url="https://tse4.mm.bing.net/th/id/OIP.dyw3Wrd_YIZgbpCB3G3KBwHaLH?pid=Api&P=0&h=220"; file="bathroom-sink-3.jpg"},
    @{url="https://tse3.mm.bing.net/th/id/OIP.ub3lxDU_D0Kd0iC0fVOxwgAAAA?pid=Api&P=0&h=220"; file="kitchen-countertop-1.jpg"},
    @{url="https://tse2.mm.bing.net/th/id/OIP.AV8UySdtJ3gsOD0Hp5X0XwHaI-?pid=Api&P=0&h=220"; file="kitchen-faucet-1.jpg"},
    @{url="https://tse1.mm.bing.net/th/id/OIP.B1_AafJ6iyxrkGMG8p8pBgHaDt?pid=Api&P=0&h=220"; file="kitchen-storage-1.jpg"},
    @{url="https://tse3.mm.bing.net/th/id/OIP.7joYthHwHeXgOFXmqn0zrQHaJQ?pid=Api&P=0&h=220"; file="kitchen-island-1.jpg"}
)

foreach ($image in $images) {
    try {
        Write-Host "Downloading $($image.file)..."
        Invoke-WebRequest -Uri $image.url -OutFile $image.file
        Write-Host "Successfully downloaded $($image.file)"
    }
    catch {
        Write-Host "Failed to download $($image.file): $($_.Exception.Message)"
    }
}

Write-Host "Download completed!" 