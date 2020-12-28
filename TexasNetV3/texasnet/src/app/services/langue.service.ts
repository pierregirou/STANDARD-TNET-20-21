import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

import { Router } from '@angular/router';
@Injectable()
export class LangueService{
    langueSelect:number=1; //permet de déterminer la langue du site par défaut 1
    langueSelectSubject=new Subject<number>();
    constructor(private router:Router){ }

    emitLangueSelect(){
        this.langueSelectSubject.next(this.langueSelect);
    }

    changeLangue(select:number){
        if(select===1){
            this.langueSelect=1;
            this.emitLangueSelect();
        }
        if(select===2){
            this.langueSelect=2;
            this.emitLangueSelect();
        }
        
        this.router.navigate(['contenu/accueil'])
    }

    getLangue(){
      return this.langueSelect === 2 ? "ANG" : "FRA"
    }
}
