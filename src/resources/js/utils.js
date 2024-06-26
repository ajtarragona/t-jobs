_d = function(...args) {
    // args.forEach(arg => {
    //     console.log(Alpine.raw(arg));
    // });

    var args=args.map((arg)=>{
        return Alpine.raw(arg);
    });

    console.log(...args);
}

baseUrl = function() {
    const urlTag =  document.head.querySelector('meta[name="base-url"]');
    if (urlTag) {
        return urlTag.content
    }

    return window.livewire_url ?? undefined
}

getCsrfToken= function() {
    const tokenTag = document.head.querySelector('meta[name="csrf-token"]')

    if (tokenTag) {
        return tokenTag.content
    }

    return window.livewire_token ?? undefined
}