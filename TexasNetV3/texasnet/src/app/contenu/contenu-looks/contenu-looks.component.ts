import { Component, OnInit, OnDestroy,HostListener, ViewChild } from '@angular/core';
import { Produits } from '../../models/produits.model';
import { Subscription } from 'rxjs';
import { CommandeService } from '../../services/commandes.service';
import { Router } from '@angular/router';
import { ImageService } from '../../services/images.service';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../../services/http-request.service';
import { Looks } from '../../models/looks.models'
import { DetailService } from '../../services/detail-produit.service'
import { TemplateService } from '../../services/template.service';

@Component({
  selector: 'app-contenu-looks',
  templateUrl: './contenu-looks.component.html',
  styleUrls: ['./contenu-looks.component.css']
})
export class ContenuLooksComponent implements OnInit {

  isDesktop:boolean;
  produits:Produits[]=[];  
  looks:Looks[]=[];
  visGalerie:boolean;
  tailleProduit:number; //permet de connaitre le nombre de produits du model Produits
  produitSubscription:Subscription;
  affichage:number=12; //variable pour récupèrer les 12 premiers produits infinite scroll
  i:number=0
  indice1:number=0;
  indice2:number=4;
  produitsTab:Produits[]=[]; //array produits spécifique pour tablette tactile
  produitTabTaille:number; //permet de connaitre le nombre de produits dans l'array et d'adapter la mise en page dans le template
  nextLimit:number; //permet de savoir quand désactiver le bouton next
  coProduit:string=""; //permet de connaitre le libelle du produit dans la modal commande express
  coImage:string=""; //permet de connaitre l'image de produit dans commande express
  tailleEcran:number=1; //Variable permettant de connaitre le nombre de produits à afficher
  tailleArray:any[]; //permet de connaître le nombre de coloris par produit
  tailleP:any[]; //permet de connaitre les tailles disponibles par produit
  detailProduit:any[]; //array contenant les détails du produit
  quantite:number=0; //quantité détail produit en fonction de la taille
  prix:number=0; //prix détail produit en fonction de la taille 
  nbColori:number; //permet de connaitre le nombre de colori pour chaque produit
  arrayColori:any[]; //permet de faie une bou=cle dans le template en fonction du nombre de colori présent
  tailleT:any[]; //tableau de taille des produits;
  tailleDisponible:string=" ";
  detailP:any[];
  arrayTaille:any[]=[];
  commandeExpress:boolean=false;
  commandeExpressSubscription:Subscription;
  titreLook:string;
  imageLook:string;
  coRefProduit:string;
  testScrollDown:boolean=false;
  mode:string; //permet de définir dans la page produit si on est en mode tableau ou ligne
  produitLookInfo:any[]=[];
  contenuColor:string;
  contenuColorSubscription:Subscription;
 constructor(private templateService:TemplateService,private imageService:ImageService,private router:Router, private commandeService:CommandeService,private httpClient:HttpClient, private httpRequest:HttpRequest, private detailService:DetailService) {

}

ngOnInit() {
  this.templateService.getContenuColor();
  this.contenuColorSubscription=this.templateService.contenuColorSubject.subscribe(
    (contenuColor:string)=>{
      this.contenuColor='#'+contenuColor;
    }
  );
  this.templateService.emitContenuColor();
    this.httpClient.post(this.httpRequest.InfoLook,{
        "login":sessionStorage.getItem("loginCompte")
    }).subscribe(data=>{
        var keys = Object.entries(data); //transforme l'objet data retourné par httpClient en un tableau
        this.looks=[]; //rénitialise le tableau produit à chaque appel de la fonction
        for (let i=0;i<keys.length;i++){
          var codeLook = keys[i][0];
          this.looks[i]=new Looks(keys[i][0],Object.entries(keys[i][1]),this.imageService.LooksArt+"/"+codeLook+"-1.jpeg");
        }
    })
  }

  onLook(titre:string){ 
    this.titreLook=titre;
    this.imageLook=this.imageService.LooksArt+"/"+titre+"-1.jpeg";
    for(let i=0;i<this.looks.length;i++){
      if (this.looks[i].titre === titre){
        this.produitLookInfo = this.looks[i].produit;
      }
    }
  }

}

