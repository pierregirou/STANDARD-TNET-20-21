import { Component, OnInit } from '@angular/core';
import { CommandeService } from '../services/commandes.service';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../services/http-request.service';

@Component({
  selector: 'app-confirm-box',
  templateUrl: './confirm-box.component.html',
  styleUrls: ['./confirm-box.component.css']
})
export class ConfirmBoxComponent implements OnInit {

  constructor(private httpRequest:HttpRequest, private httpClient:HttpClient, private commandeService:CommandeService) { }

  ngOnInit() {
  }

  closeConfirm(){
    this.commandeService.displayConfirmBox=false;
    this.commandeService.emitDisplayConfirm();
  }

  confirm(){
    this.httpClient.post(this.httpRequest.UpdatePanier,{
      "login":sessionStorage.getItem("loginCompte"),
      "action":"deleteAll"
    }).subscribe(data=>{
      this.commandeService.getCommande();
      this.commandeService.emitCommande();
    })
    this.closeConfirm();
  }

}
