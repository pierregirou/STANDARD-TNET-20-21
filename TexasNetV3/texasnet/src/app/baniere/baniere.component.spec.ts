import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BaniereComponent } from './baniere.component';

describe('BaniereComponent', () => {
  let component: BaniereComponent;
  let fixture: ComponentFixture<BaniereComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BaniereComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BaniereComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
