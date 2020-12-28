import { PipeTransform, Pipe } from '@angular/core'
import { DomSanitizer } from '@angular/platform-browser';
import { isUndefined } from 'util';

@Pipe({name:'safeHtml'})
export class safeHtmlPipe implements PipeTransform{
    constructor(private sanitier:DomSanitizer){}
    transform(value){
        return this.sanitier.bypassSecurityTrustHtml(value);
    }
}

@Pipe({
    name: 'myfilter'
})
export class productFilter implements PipeTransform{
    produits : any[];
    transform(items: any[], filter: Object): any {
        if(!isUndefined(filter)){
            this.produits=[];
            var i=0;
            items.forEach(item => {
                if (item.libelle.indexOf(filter)>-1 || item.refproduit.indexOf(filter)>-1){
                    this.produits[i] = item;
                    i++;
                    
                }
            });
            return this.produits;
        } else {
            return items;
        }
    }
}
