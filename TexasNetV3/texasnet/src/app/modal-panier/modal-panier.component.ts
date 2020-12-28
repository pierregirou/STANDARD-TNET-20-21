import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommandeService } from '../services/commandes.service';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from "../services/http-request.service";
import { ImageService } from "../services/images.service";
import { Subscription } from 'rxjs';
import { DetailService } from '../services/detail-produit.service';
import { Router, NavigationEnd } from '@angular/router';

@Component({
  selector: 'app-modal-panier',
  templateUrl: './modal-panier.component.html',
  styleUrls: ['./modal-panier.component.css']
})
export class ModalPanierComponent implements OnInit, OnDestroy {

  constructor(public imageService:ImageService,private commandeService:CommandeService,private httpClient:HttpClient,private httpRequest:HttpRequest, private router: Router) { }
  quantitePanier:number=0;
  montantPanier:number=0;
  libelleProduitAjout:string="";
  tailleProduitAjout:string="";
  tailleMinProduitAjout:string='';
  tailleMaxProduitAjout:string='';
  quantiteProduitAjout:number=0;
  totalProduitAjout:number=0;
  refProduitAjout:string="";
  codeSaisonAjout:string="";
  codeColorisAjout:string="";
  idDernierProduitAjout:number;
  idDernierProduitAjoutSubscription:Subscription;
  imageArtModal:string="";

  ngOnInit() {
    this.idDernierProduitAjoutSubscription=this.commandeService.idDernierProduitAjoutSubject.subscribe(
      (idDernierProduitAjout:number)=>{
        this.idDernierProduitAjout=Number(idDernierProduitAjout);
        this.commandeService.recupCommande().then(data=>{
          for(let i=0;i<Object.keys(data[2]).length;i++){
            this.imageArtModal = data[2][i].imageArt;
            //if(Number(data[2][i].idDetailProduit)===Number(idDernierProduitAjout)){
              this.refProduitAjout=data[2][i].refproduit;
              this.codeSaisonAjout=data[2][i].saison;
              this.libelleProduitAjout=data[2][i].libelle;
              //this.tailleProduitAjout=data[2][i].taille;
              this.tailleMinProduitAjout=data[2][i].tailleMin;
              this.tailleMaxProduitAjout=data[2][i].tailleMax;
              this.quantiteProduitAjout=data[2][i].quantite;
              if(sessionStorage.getItem("codeTarifClient")==="true"){
                this.totalProduitAjout=Number((data[2][i].prix )*(data[2][i].quantite));
              }else{
                this.totalProduitAjout=Number((data[2][i].prix)*(data[2][i].quantite));
              }
              this.totalProduitAjout=Number(this.totalProduitAjout.toFixed(2));
              this.quantitePanier=data[1].pieces;
              this.montantPanier=data[1].montant;
           // }
          }
        });
      }
    );
    this.commandeService.emitIdDernierProduitAjout();


    // rajout pour AMATEIS car il ne veulent pas la modal panier qui ralentie la prise de commande des VRP
    //this.closeModalPanier();





/*     this.router.events.subscribe((val) => {
      var change = val instanceof NavigationEnd;

      if(change){
        this.router.navigate([val['url']]);
        window.location.reload();
      }
    }); */
  }

  closeModalPanier(){
    this.commandeService.closeModalPanier();
    this.commandeService.getCommande();
    this.commandeService.emitCommande();

  }

  ngOnDestroy(){ }


}
