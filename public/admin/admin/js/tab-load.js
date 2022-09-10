function load_tab(tabId = null, data_options = null, tabIndex = -1, tabHash = null) {
    if (!tabId) {
        tabId = $('[data-loadable]').attr('id');
    }

    if ($(`#${tabId}`).length == 0) {
        alert('L\'élément avec l\'ID '+ tabId + ' n\'existe pas');
        return;
    }
   
    const keyBase = tabId.replace('-', '_');
    const indexKey = `${keyBase}_current_index`;
    const hashKey = `setwork_${keyBase}_current_hash`;
    const urlKey = `setwork_${keyBase}_current_url`;

    const currentIndex = localStorage.getItem(indexKey);
    
    function load_content(url, hash, method = 'GET') {
        localStorage.setItem(hashKey, hash);
        localStorage.setItem(urlKey, url);
       
        $.ajax({
            url: url,
            cache: false,
            //method: method || 'GET',
            beforeSend: function () {
                $('.tab-pane', '#' + tabId).empty().html('');
                $(`#${hash}`).html(`
                    <div class="d-flex flex-row justify-content-center tab-loader-text">
                        <div class="p-2">
                            <div class="spinner spinner-primary  spinner-track spinner-lg"></div> 
                        </div>
                        <div class="p-2">Chargement des données</div>
                        
                    </div>
                    
                      
                    
                `);
            },
            success: function (content) {
                $(`#${hash}`).empty().html(content);
            },
            error: function () {
               $(`#${hash}`).empty().html(`
               <div class="alert alert-custom alert-notice alert-light-danger fade show" role="alert">
               <div class="alert-icon"><i class="flaticon-warning"></i></div>
               <div class="alert-text">Erreur interne du serveur</div>
              
           </div>
               `);
           }
        });
    }
    const hash_url = document.location.hash.slice(1);
    const hash =  hash_url || localStorage.getItem(hashKey);

   
    const url = localStorage.getItem(urlKey);

    if (tabIndex >= 0 || (hash && $('[href="#'+ hash +'"]').length && (hash_url || $('[href="#'+ hash +'"]').data('href') == url))) {
        const $active_tab_link = $('[href="#'+ hash +'"]');
        if (tabIndex >= 0 || tabHash) {
            let active_url, $active_tab_link, hash;
            if (tabIndex >= 0) {
                $(`#${tabId} li:eq(${tabIndex}) a`).tab('show'); // Select third tab (0-indexed)
                $active_tab_link = $(`#${tabId} a.active`);
                [, hash] = $active_tab_link.attr('href').split('#');
                active_url = $active_tab_link.data('href');
            } else {
                $active_tab_link = $('[href="'+tabHash+'"]');
                $active_tab_link.tab('show');
                active_url = $active_tab_link.data('href');
            }
            
    
            load_content(active_url, hash, $active_tab_link.data('method'));
        } else {
            if ($active_tab_link.length) {
                const $li_parent = $active_tab_link.closest('li');
   
           //console.log($active_tab_link);
   
               $active_tab_link.tab('show');
               load_content(hash_url  ? $('[href="#'+ hash +'"]').data('href') : url ,  hash, $('[href="#'+ hash +'"]').data('method'));
           }
          
        }
        

    }  else {
       
       $(`#${tabId} li:eq(0) a`).tab('show'); // Select third tab (0-indexed)
        const $active_tab_link = $(`#${tabId} a.active`);
       
        const [, hash] = $active_tab_link.attr('href').split('#');
        const active_url = $active_tab_link.data('href');

        load_content(active_url, hash, $active_tab_link.data('method'));
    }
    $(document)
    .on('click', '.nav-tab-links a', (e) => e.stopImmediatePropagation())
    .on('show.bs.tab', function (e) {
        
    })
    .on('shown.bs.tab', `#${tabId}`, function (e) {
      
        e.stopImmediatePropagation();
        const target = e.target;
        //const previousTarget = e.relatedTarget;
        const $target = $(target);
        const [, hash] = target.href.split('#');
          const previousTarget = e.relatedTarget;

        if (previousTarget) {
            const [, oldHash] = previousTarget.href.split('#');
             $('#' + oldHash).empty().html('');
        }


        localStorage.setItem(indexKey, $target.closest('li').index());
        //localStorage.setItem('old_denombrement_type', $target.data('type'));

        load_content($target.data('href'), hash, $target.data('method'));
        
    });
}
    