import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { GestionFdpComponent } from './gestion-fdp.component';

describe('GestionFdpComponent', () => {
  let component: GestionFdpComponent;
  let fixture: ComponentFixture<GestionFdpComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ GestionFdpComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(GestionFdpComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
