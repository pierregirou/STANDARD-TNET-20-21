import { Component, OnInit, ViewChild } from '@angular/core';
import { FormControl } from '@angular/forms';
import { HttpRequest } from '../../../services/http-request.service';
import { HttpClient } from '@angular/common/http';


@Component({
  selector: 'app-gestion-fdp',
  templateUrl: './gestion-fdp.component.html',
  styleUrls: ['./gestion-fdp.component.css']
})

export class GestionFdpComponent implements OnInit {
  paysInfo = new FormControl();
  PaysList: string[] = [];

  addFDP:any;

  constructor(private httpRequest:HttpRequest,private httpClient:HttpClient) {

  }

  ngOnInit() {
    this.httpClient.post(this.httpRequest.InfoPays,{
      "login":sessionStorage.getItem("loginCompte")
    }).subscribe(data=>{
      let arrayPays=[];
      arrayPays= Object.values(data);
      for(let i =0; i < arrayPays.length;i++) {
        this.PaysList[i] = arrayPays[i]['nomPays']
      }
    });
  }

  changeFDPvalue(valeur,index,type){
    //aCalculer:valeur.checked
    //var object1 = {valeur:valeur};
    this.addFDP[index][type] = valeur
  }

}
