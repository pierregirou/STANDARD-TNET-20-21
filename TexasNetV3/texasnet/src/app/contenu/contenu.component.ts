import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ModuleService } from '../services/modules.service';
import { Produits } from '../models/produits.model';
import { Subscription } from 'rxjs';
import { ProduitService } from '../services/produits.service';
import { MenuService } from '../services/menu.service';

@Component({
  selector: 'app-contenu',
  templateUrl: './contenu.component.html',
  styleUrls: ['./contenu.component.css']
})
export class ContenuComponent implements OnInit {
  name:string;
  menuSnap:string;
  menuSnapAction:string;
  maintenance:boolean;
  afficherFiltres:boolean;

  constructor(private route:ActivatedRoute, private router : Router, private moduleService : ModuleService, private produitService:ProduitService, private menuService:MenuService) {
    route.params.subscribe( //On crée un subscribe qui va modifier le contenu de menuSnap à chaque changement de route
      (value)=>{ //value contient le params de la route /contenu/:menu
        this.menuSnap=value.menu; //menuSnap prend la valeur :menu
        this.menuSnapAction=value.action; //menuSnapAction prend la valeur /contenu/:action
        //Test si la route est existante sinon renvoie sur 4040
        if((value.menu!=='accueil')&&(value.menu!=='produits')&&(value.menu!=='promo')&&(value.menu!=='telechargements')&&(value.menu!=='panier')&&(value.menu!=='points')&&(value.menu!=='compte')&&(value.action!=='modificationC')&&(value.action!=='modificationMDP')&&(value.action!=='history')&&(value.menu!=='looks')&&(value.action!=='retourCommande')){
          this.router.navigate(['/not-found']);
        }
      }
    )
  }

  ngOnInit() {
    this.moduleService.enMaintenance().then(
      (data:boolean)=>{
        this.maintenance=data;
      })

      // determine si on affiche les filtres
      this.moduleService.getAfficherFiltres().then(
        (data:boolean)=>{
            this.afficherFiltres=data;
        }
      )
  }

}
